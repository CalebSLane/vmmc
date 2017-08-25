package org.itech.vmmc;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Fragment;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;


/**
 * A simple {@link Fragment} subclass.
 * Activities that contain this fragment must implement the
 * {@link LoginFragment.OnFragmentInteractionListener} interface
 * to handle interaction events.
 * Use the {@link LoginFragment#newInstance} factory method to
 * create an instance of this fragment.
 */
public class LoginFragment extends Fragment {
    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    //private static final String ARG_PARAM1 = "param1";
    //private static final String ARG_PARAM2 = "param2";

    public static String LOG = "gnr";
//    public static String deviceId = "";

    //public DBHelper dbHelp = new DBHelper(getActivity());
    // moved to onCreate: http://stackoverflow.com/questions/23449384/getwritabledatabase-throwing-null-pointer-exception-in-my-apps
    public DBHelper _dbHelp;

    // TODO: Rename and change types of parameters
    private String mParam1;
    private String mParam2;

    private OnFragmentInteractionListener mListener;

    /**
     * Id to identity READ_CONTACTS permission request.
     */
    private static final int REQUEST_READ_CONTACTS = 0;

    /**
     * A dummy authentication store containing known user names and passwords.
     * TODO: remove after connecting to a real authentication system.
     */
//    private static final String[] DUMMY_CREDENTIALS = new String[]{
//            "user@itech.org:password", "bar@example.com:world", "u@:pa"
//    };
    /**
     * Keep track of the login task to ensure we can cancel it if requested.
     */
    private LoginFragment.UserLoginTask mAuthTask = null;

    // UI references.
    private AutoCompleteTextView mEmailView;
    private EditText mPasswordView;
    private View mProgressView;
    private View mLoginFormView;

    /**
     * Use this factory method to create a new instance of
     * this fragment using the provided parameters.
     *
     * param param1 Parameter 1.
     * param param2 Parameter 2.
     * @return A new instance of fragment LoginFragment.
     */
    // TODO: Rename and change types and number of parameters
    public static LoginFragment newInstance() {
        LoginFragment fragment = new LoginFragment();
        Bundle args = new Bundle();
        //args.putString(ARG_PARAM1, param1);
        //args.putString(ARG_PARAM2, param2);
        fragment.setArguments(args);
        return fragment;
    }

    public LoginFragment() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            //mParam1 = getArguments().getString(ARG_PARAM1);
            //mParam2 = getArguments().getString(ARG_PARAM2);
        }

        Log.d(LOG, "LoginFragment.onCreate0");

        DBHelper dbHelp = new DBHelper(getActivity());

        this._dbHelp = dbHelp;

        Log.d(LOG, "LoginFragment.onCreate1");

//        final TelephonyManager tm = (TelephonyManager) getActivity().getSystemService(Context.TELEPHONY_SERVICE);
//
//        final String tmDevice, tmSerial, androidId;
//        tmDevice = "" + tm.getDeviceId();
//        tmSerial = "" + tm.getSimSerialNumber();
//        androidId = "" + android.provider.Settings.Secure.getString(getActivity().getContentResolver(), android.provider.Settings.Secure.ANDROID_ID);
//
//        UUID deviceUuid = new UUID(androidId.hashCode(), ((long) tmDevice.hashCode() << 32) | tmSerial.hashCode());
//        deviceId = deviceUuid.toString();
//        Log.d(LOG, "loginFragemnt:onCreate:id: " + deviceId);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_login, container, false);

        getActivity().setTitle(getResources().getString(R.string.loginTitle));
        // Inflate the layout for this fragment

        // Set up the login form.
        mEmailView = (AutoCompleteTextView) view.findViewById(R.id.email);

        mPasswordView = (EditText) view.findViewById(R.id.password);
        mPasswordView.setOnEditorActionListener(new TextView.OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView textView, int id, KeyEvent keyEvent) {
                if (id == R.id.login || id == EditorInfo.IME_NULL) {
                    attemptLogin();
                    return true;
                }
                return false;
            }
        });

        Button mEmailSignInButton = (Button) view.findViewById(R.id.email_sign_in_button);
        mEmailSignInButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                attemptLogin();
            }
        });

        mLoginFormView = view.findViewById(R.id.login_form);
        mProgressView = view.findViewById(R.id.login_progress);

        Button btnLogin = (Button) view.findViewById(R.id.email_sign_in_button);
        btnLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Log.d(TAG, "CLICK");
                attemptLogin();
            }
        });

        Button btnLogout = (Button) view.findViewById(R.id.email_sign_out_button);
        btnLogout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mEmailView.setText((CharSequence) "");
                mPasswordView.setText((CharSequence) "");
                MainActivity.LOGGED_IN = false;
            }
        });

        return view;
    }

     private boolean mayRequestContacts() {
        /*
        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.M) {
            return true;
        }
        if (checkSelfPermission(READ_CONTACTS) == PackageManager.PERMISSION_GRANTED) {
            return true;
        }
        if (shouldShowRequestPermissionRationale(READ_CONTACTS)) {
            Snackbar.make(mEmailView, R.string.permission_rationale, Snackbar.LENGTH_INDEFINITE)
                    .setAction(android.R.string.ok, new View.OnClickListener() {
                        @Override
                        @TargetApi(Build.VERSION_CODES.M)
                        public void onClick(View v) {
                            requestPermissions(new String[]{READ_CONTACTS}, REQUEST_READ_CONTACTS);
                        }
                    });
        } else {
            requestPermissions(new String[]{READ_CONTACTS}, REQUEST_READ_CONTACTS);
        }
        */
        return false;
    }

     /**
     * Attempts to sign in or register the account specified by the login form.
     * If there are form errors (invalid email, missing fields, etc.), the
     * errors are presented and no actual login attempt is made.
     */
    private void attemptLogin() {
        Toast.makeText(getView().getContext(), getString(R.string.logging_in), Toast.LENGTH_SHORT).show();

        Log.d(LOG, "attemptLogin0");

        // not using async login
//        if (mAuthTask != null) {
//            return;
//        }

        // Reset errors.
        mEmailView.setError(null);
        mPasswordView.setError(null);

        // Store values at the time of the login attempt.
//        Log.d(LOG, "LoginFragment: change back to test real values, L224");
        String email = mEmailView.getText().toString();
        String password = mPasswordView.getText().toString();
//        String email = "u@";
//        String password = "pa";

        boolean cancel = false;
        View focusView = null;

        // Check for a valid password, if the user entered one.
        if (!TextUtils.isEmpty(password) && !isPasswordValid(password)) {
            mPasswordView.setError(getString(R.string.error_invalid_password));
            focusView = mPasswordView;
            cancel = true;
        }

//        Log.d(LOG, "LoginFragment: uncomment field checks, L240");
        if (TextUtils.isEmpty(password)) {
            mPasswordView.setError(getString(R.string.error_field_required));
            focusView = mPasswordView;
            cancel = true;
        } else if (!isPasswordValid(password)) {
            mPasswordView.setError(getString(R.string.error_invalid_password));
            focusView = mPasswordView;
            cancel = true;
        }

        // Check for a valid email address.
        if (TextUtils.isEmpty(email)) {
            mEmailView.setError(getString(R.string.error_field_required));
            focusView = mEmailView;
            cancel = true;
        } else if (!isEmailValid(email)) {
            mEmailView.setError(getString(R.string.error_invalid_email));
            focusView = mEmailView;
            cancel = true;
        }

        if (cancel) {
            // There was an error; don't attempt login and focus the first
            // form field with an error.
            focusView.requestFocus();
        } else {
            // Show a progress spinner, and kick off a background task to
            // perform the user login attempt.
            // showProgress(true);
             mAuthTask = new UserLoginTask(email, password);
             mAuthTask.execute();


        }
    }

    private boolean isEmailValid(String email) {
        //TODO: Replace this with your own logic
        return email.contains("@");
        // return true;
    }

    private boolean isPasswordValid(String password) {
        //TODO: Replace this with your own logic
        return password.length() > 1;
    }

    // TODO: Rename method, update argument and hook method into UI event
    public void onButtonPressed(int position) {
        if (mListener != null) {
            mListener.onFragmentInteraction(position);
        }
    }

    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
        try {
            mListener = (OnFragmentInteractionListener) activity;
        } catch (ClassCastException e) {
            throw new ClassCastException(activity.toString()
                    + " must implement OnFragmentInteractionListener");
        }
    }

    @Override
    public void onDetach() {
        super.onDetach();
        mListener = null;
    }

    /**
     * This interface must be implemented by activities that contain this
     * fragment to allow an interaction in this fragment to be communicated
     * to the activity and potentially other fragments contained in that
     * activity.
     * <p/>
     * See the Android Training lesson <a href=
     * "http://developer.android.com/training/basics/fragments/communicating.html"
     * >Communicating with Other Fragments</a> for more information.
     */
    public interface OnFragmentInteractionListener {
        // TODO: Update argument type and name
        public void onFragmentInteraction(int position);
    }

    public void showMessage(String title, String message) {
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        builder.setCancelable(true);
        builder.setTitle(title);
        builder.setMessage(message);
        builder.show();
    }

    public void onResume() {
        super.onResume();
        Log.d(LOG, "login fragment:onResume: pop " );

        getView().setFocusableInTouchMode(true);
        getView().requestFocus();
        getView().setOnKeyListener(new View.OnKeyListener() {
            @Override
            public boolean onKey(View v, int keyCode, KeyEvent event) {
                if (event.getAction() == KeyEvent.ACTION_UP && keyCode == KeyEvent.KEYCODE_BACK){
//                     handle back button
                    Log.d(LOG, "login fragment:onResume: pop: handle back " + MainActivity.LOGGED_IN );
                    if(MainActivity.LOGGED_IN==false){
                        getActivity().finish();
                        System.exit(0);
                    }
                    return true;
                }
                return false;
            }
        });
    }

    public class UserLoginTask extends AsyncTask<Void, Void, Boolean> {

        private final String mEmail;
        private final String mPassword;

        UserLoginTask(String email, String password) {
            mEmail = email;
            mPassword = password;
        }

        @Override
        protected Boolean doInBackground(Void... params) {
            Log.d(LOG, "check credentials0");
            DBHelper dbHelp = new DBHelper(getActivity());
            String credentials = dbHelp.getUserCredentials(mEmail);

            PasswordUtil passwordUtil = new PasswordUtil();
            if (credentials.equals("")) {
                String passwordHash = passwordUtil.secureHashPassword("password");
                credentials = "sync@:" + passwordHash;
            }

            String[] pieces = credentials.split(":");
            boolean loginSuccess = false;
            try {
                loginSuccess = pieces[0].equals(mEmail) && passwordUtil.verifyPassword(mPassword, pieces[1]);
            } catch (Exception e) {
                Log.d(LOG, "ERROR while checking password hash " + e.toString());
                Log.d(LOG, "attempting unencrypted check");
                loginSuccess = pieces[0].equals(mEmail) && pieces[1].equals(mPassword);
                if (loginSuccess) {
                    User user = new User(dbHelp, mEmail + ":" + mPassword);
                    pieces[1] = passwordUtil.secureHashPassword(mPassword);
                    Log.d(LOG, "password at loginFragment " + pieces[1]);
                    user.set_password(pieces[1]);
                    dbHelp.updateUser(user);
                }
            } finally {
                if (loginSuccess) {
                    Log.d(LOG, "password check success");
                    MainActivity.LOGGED_IN = true;
                    MainActivity._username = mEmail; // referred to as username in db, email in pop-up
                    MainActivity._password = pieces[1];

                    if (!MainActivity._username.equals("sync@")) {
                        MainActivity.USER_OBJ = new User(dbHelp, MainActivity._username + ":" + MainActivity._password);
                    }
                    return true;
                }
            }
            // Log.d(TAG, "bad");
            return false;
        }

        @Override
        protected void onPostExecute(Boolean result) {
            // Log.d(TAG, "Logged In?:" + MainActivity.LOGGED_IN);
            if (!MainActivity.LOGGED_IN){
                Toast.makeText(getActivity(), getActivity().getString(R.string.error_invalid_login), Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(getActivity(), getActivity().getString(R.string.valid_login), Toast.LENGTH_SHORT).show();
                Fragment fragment = getActivity().getFragmentManager().findFragmentByTag(ActionFragment.TAG);
                if (fragment == null) {
                    fragment = ActionFragment.newInstance("main", "");
                    getActivity().getFragmentManager().beginTransaction().replace(R.id.container, fragment, ActionFragment.TAG).addToBackStack(MainActivity.currentFragmentId).commit();
                } else {
                    getActivity().getFragmentManager().beginTransaction().replace(R.id.container, fragment, ActionFragment.TAG).commit();
                }
                MainActivity.currentFragmentId = "Action";// auto sync
//                Toast.makeText(getView().getContext(), getString(R.string.sync_reminder), Toast.LENGTH_SHORT).show();
// uncomment to show sync on login, gnr
//                if(MainActivity._pass.equals("")) {
//
//                    AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
//                    builder.setTitle("Sync Password");
//
//                    final EditText input = new EditText(getActivity());
//                     Specify the type of input expected; this, for example, sets the input as a password, and will mask the text
//                    input.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_PASSWORD);
//                    builder.setView(input);
//
//                     Set up the buttons
//                    builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
//                        @Override
//                        public void onClick(DialogInterface dialog, int which) {
//                            MainActivity._pass = input.getText().toString();
//                            Log.d(LOG, "_pass: " + MainActivity._pass);
//                            DBHelper dbHelp = new DBHelper(getActivity());
//                            dbHelp.doSyncDB();
//                        }
//                    });
//                    builder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
//                        @Override
//                        public void onClick(DialogInterface dialog, int which) {
//                            dialog.cancel();
//                        }
//                    });
//
//                    builder.show();
//                }
            }
        }
    }
}
