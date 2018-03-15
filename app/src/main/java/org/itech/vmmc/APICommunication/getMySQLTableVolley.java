package org.itech.vmmc.APICommunication;

import android.app.NotificationManager;
import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteStatement;
import android.support.v4.app.NotificationCompat;
import android.util.Log;
import android.widget.Toast;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;

import org.itech.vmmc.MainActivity;
import org.itech.vmmc.R;
import org.itech.vmmc.SyncTableObjects;
import org.itech.vmmc.VolleySingleton;
import org.itech.vmmc.SyncTableObjects.*;

import org.itech.vmmc.DBHelper;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by Caleb on 21/07/2017.
 */

public class getMySQLTableVolley {

    public Context _context;
    public SQLiteDatabase _db;
    DBHelper dbhelp;
    LoginManager loginManager;

    public static String LOG = "csl";

    NotificationManager mNotifyManager;
    NotificationCompat.Builder mBuilder;

    int SOCKET_TIMEOUT_MS = 30000;

    public getMySQLTableVolley(Context context, DBHelper dbHelper) {
        this._context = context;
        this.dbhelp = dbHelper;
        this._db = dbHelper.getWritableDatabase();
        loginManager = new LoginManager(_context);

        mNotifyManager =
                (NotificationManager) _context.getSystemService(_context.NOTIFICATION_SERVICE);
        mBuilder = new NotificationCompat.Builder(_context);
    }

    public void getAllTables() {
        mBuilder.setContentTitle("Data Download")
                .setContentText("Download in progress")
                .setSmallIcon(R.drawable.download);
        //if no jwt, attempt login. getTables called in login response handler
        if (!loginManager.hasValidJWT()) {
            loginManager.logIn(new NetworkResponseCallback() {
                @Override
                public void onSuccess() {
                    getSyncTables();
                }
            }, MainActivity._user, MainActivity._pass);
        } else {
            getSyncTables();
        }
    }


    //gets all tables in regular database sync
    public void getSyncTables() {
        SyncTableObjects  syncTableObjects = new SyncTableObjects();
        getTable(syncTableObjects.personTableInfo);
        getTable(syncTableObjects.userTableInfo);
        getTable(syncTableObjects.userTypeTableInfo);
        getTable(syncTableObjects.userToAclTableInfo);
        getTable(syncTableObjects.aclTableInfo);
        getTable(syncTableObjects.clientTableInfo);
        getTable(syncTableObjects.facilitatorTableInfo);
        getTable(syncTableObjects.locationTableInfo);
        getTable(syncTableObjects.addressTableInfo);
        getTable(syncTableObjects.regionTableInfo);
        getTable(syncTableObjects.bookingTableInfo);
        getTable(syncTableObjects.interactionTableInfo);
        getTable(syncTableObjects.facilitatorTypeTableInfo);
        getTable(syncTableObjects.procedureTypeTableInfo);
        getTable(syncTableObjects.followupTableInfo);
        getTable(syncTableObjects.interactionTypeTableInfo);
        getTable(syncTableObjects.statusTypeTableInfo);
        getTable(syncTableObjects.institutionTableInfo);
        getTable(syncTableObjects.groupActivityTableInfo);
        getTable(syncTableObjects.groupTypeTableInfo);
    }

    private void getTable(JSONObject tableInfo) {
        try {
            final String dataTable = tableInfo.getString("dataTable");
            final JSONArray fields = tableInfo.getJSONArray("fields");
            final String url = MainActivity.INDEX_URL + "/" + dataTable;
            Log.d(LOG, "GET table request at " + url);

            //attach jwt and make send request
            AuthenticatedRequest request = new AuthenticatedRequest
                    (Request.Method.GET, url, null,
                            new Response.Listener<JSONObject>() {
                                @Override
                                public void onResponse(JSONObject response) {
                                    try {
                                        if (response.getString(MainActivity.TAG_SUCCESS).equals("0")) {
                                            Log.d(LOG, "Server returned ERROR for GET "
                                                    + dataTable + ": " + response.getString(MainActivity.TAG_MESSAGE));
                                            if (response.getString(MainActivity.TAG_MESSAGE).contains("jwt")) {
                                                if (loginManager.hasValidJWT()) {
                                                    loginManager.invalidateJWT();
                                                    MainActivity._pass = "";
                                                    Toast.makeText(_context, _context.getResources().getString(R.string.failed_sync_jwt), Toast.LENGTH_LONG).show();
                                                }
                                            }
                                        } else {
                                            insertData(response, dataTable, fields);
                                        }

                                    } catch (JSONException e) {
                                        Log.d(LOG, "exception > GET request for: " + dataTable);
                                        Log.d(LOG, "exception > Fields: " + fields.toString());
                                        Log.d(LOG, "exception > received JSONObject" + response.toString());
                                        e.printStackTrace();
                                    } catch (NullPointerException e) {
                                        Log.d(LOG, "exception > GET request for: " + dataTable);
                                        Log.d(LOG, "exception > Fields: " + fields.toString());
                                        e.printStackTrace();
                                    }
                                }
                            }
                            , new Response.ErrorListener() {
                        @Override
                        public void onErrorResponse(VolleyError error) {
                            Log.d(LOG, "error on GET at " + url);
                            error.printStackTrace();
                        }
                    }) {
            };
            request.setRetryPolicy(new DefaultRetryPolicy(
                    SOCKET_TIMEOUT_MS,
                    DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                    DefaultRetryPolicy.DEFAULT_BACKOFF_MULT)
            );
            VolleySingleton.getInstance(_context).addToRequestQueue(request);
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    private void insertData(JSONObject response, String dataTable, JSONArray fields)
            throws JSONException {
        Log.d(LOG, "Server returned success for GET "
                + dataTable);
        JSONArray results_JSONarray = response.getJSONArray("posts");
        int num_recs = results_JSONarray.length();
        int num_fields = fields.length();
        int id = 1;
        int i = 0;

        //list of fields for statement and
        //list of '?'s for using prepared statement to protect from SQLinjection
        String fields_string = fields.join(", ");
        String prepared_results_string = "";
        for (int j = 0; j < num_fields; ++j) {
            if (j == 0) {
                prepared_results_string += "?";
            } else {
                prepared_results_string += ", ?";
            }
        }
        SQLiteStatement _insert = _db.compileStatement(
                "insert or replace into " + dataTable.toLowerCase()
                        + " (" + fields_string + ") "
                        + " values(" + prepared_results_string + ");");
        //for each entry bind all params, then clear all bindings for next
        for (i = 0; i < num_recs; ++i) {
            JSONObject _rec = results_JSONarray.getJSONObject(i);
            //loop to format data for insert or replace
            for (int j = 0; j < num_fields; ++j) {
                int SQLindex = j + 1;
                _insert.bindString(SQLindex, _rec.getString(fields.getString(j)));
            }
            try {
                int incr = (int) ((i / (float) num_recs) * 100);
                mBuilder.setProgress(100, incr, false);
                mNotifyManager.notify(id, mBuilder.build());
                //Log.d(LOG, "getMySQLUserTable personInsert " + personInsert.toString() + " " + i);
                //Log.d(LOG, "getMySQLUserTable personInsert " + " " + i);

                _insert.execute();
            } catch (Exception ex) {
                Log.d(LOG, "getMySQLTable loop exception > " + ex.toString());
            }
            _insert.clearBindings();
        }
        mBuilder.setContentText(_context.getResources().getString(R.string.sync_complete) + " (" + i + " records)").setProgress(0, 0, false);
        mNotifyManager.notify(id, mBuilder.build());
    }

    private String encodeForSQL(String string) {
        if (string == null)
            return null;
        String encodedSQL = string.replace("'", "''");
        return encodedSQL;
    }
}
