package org.itech.vmmc.APICommunication;

import com.android.volley.AuthFailureError;
import com.android.volley.Response;
import com.android.volley.toolbox.JsonObjectRequest;

import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

/**
 * Created by Caleb on 10/08/2017.
 */

public class AuthenticatedRequest extends JsonObjectRequest {

    private static String LOG = "csl";
    public static String TAG = "Auth";

    public AuthenticatedRequest(int method, String url, JSONObject requestBody,
                                Response.Listener successListener, Response.ErrorListener errorListener) {
        super(method, url, requestBody, successListener, errorListener);
        this.setTag(TAG);
    }

    @Override
    public Map<String, String> getHeaders() throws AuthFailureError {
        Map<String, String> headers = new HashMap<>();
        String auth = "Bearer " + LoginManager.JWT;
        headers.put("Content-Type", "application/json");
        headers.put("Authorization", auth);
        return headers;
    }
}