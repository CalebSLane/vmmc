package org.itech.vmmc.APICommunication;

import android.util.Base64;
import android.util.Log;

import com.android.volley.AuthFailureError;
import com.android.volley.Response;
import com.android.volley.toolbox.JsonObjectRequest;

import org.json.JSONObject;

import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Map;

/**
 * Created by Caleb on 11/08/2017.
 */

public class LoginRequest extends JsonObjectRequest {
    private String base64Credentials;
    private static String LOG = "csl";
    private static String TAG = "login";

    public LoginRequest(int method, String url, JSONObject requestBody,
                        Response.Listener successListener, Response.ErrorListener errorListener){
        super(method, url, requestBody, successListener, errorListener);
        this.setTag(TAG);
    }

    @Override
    public Map<String, String> getHeaders() throws AuthFailureError {
        Map<String, String> headers = new HashMap<>();
        String auth = "Basic " + base64Credentials;
        headers.put("Authorization", auth);
        return headers;
    }

    public void setCredentials(String username, String password) {
        try {
            String credentials = username + ":" + password;
            base64Credentials = Base64.encodeToString(credentials.getBytes("utf-8"),
                    Base64.DEFAULT);
        } catch(UnsupportedEncodingException e) {
            Log.d(LOG, "Login unsuccessful: ERROR in encoding password and username");
        }
    }
}
