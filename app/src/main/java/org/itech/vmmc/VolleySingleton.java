package org.itech.vmmc;

import android.content.Context;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.Volley;

/**
 * Created by Caleb on 21/07/2017.
 */

public class VolleySingleton extends Volley {
    private static VolleySingleton mVolley;
    private RequestQueue mRequestQueue;
    private static Context mContext;

    private VolleySingleton(Context context) {
        mContext = context;
        mRequestQueue = getRequestQueue();
    }

    public static synchronized VolleySingleton getInstance(Context context) {
        if (mVolley == null) {
            mVolley = new VolleySingleton(context);
        }
        return mVolley;
    }

    public RequestQueue getRequestQueue() {
        if (mRequestQueue == null) {
            mRequestQueue = Volley.newRequestQueue(mContext.getApplicationContext());
        }
        return mRequestQueue;
    }

    public <T> void addToRequestQueue(Request<T> req) {
        getRequestQueue().add(req);
    }

    public void cancelRequestsWithTag(String tag) {
        if (mRequestQueue != null) mRequestQueue.cancelAll(tag);
    }
}
