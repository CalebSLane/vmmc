package org.itech.vmmc;

import android.app.Activity;
import android.app.Fragment;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import java.util.Calendar;
import java.util.List;


/**
 * A simple {@link Fragment} subclass.
 * Activities that contain this fragment must implement the
 * {@link EditUserFragment.OnFragmentInteractionListener} interface
 * to handle interaction events.
 * Use the {@link EditUserFragment#newInstance} factory method to
 * create an instance of this fragment.
 */
public class EditUserFragment extends Fragment implements AdapterView.OnItemSelectedListener {
    // TODO: Rename parameter arguments, choose usernames that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    public static String TAG = "EditUserTag";
    public static String LOG = "gnr";
    public Context _context;

    private static final String ARG_EDIT_USER_PARAM = "EXTRA_EDIT_USER_PARAM";
    private static final String ARG_EDIT_USER_RECORD_PARAM = "EXTRA_EDIT_USER_RECORD_PARAM";

    private static String FILLER_PASSWORD = "?????????";

    View _view;

    // private String mEditUserParam;
    private static String _editUserRecordParam;
//    private static Booking _booking;
//    private static UserType _userType;

    private static User _user;
    private static UserType _userType;
    private  TextView _username;
    private  TextView _phone;
    private  TextView _password;
    private  TextView _firstName;
    private  TextView _lastName;
    private  TextView _email;
    private  TextView _RegionId;
    private  TextView _IsBlocked;
    private  TextView _timestamp;
    private  CheckBox _cb_region;
    private  RadioGroup _rg_is_blocked;
    private  RadioButton _rb_is_blocked;
    private  CheckBox _cb_region1;
    private  CheckBox _cb_region2;
    private  RadioButton _rb_yes;
    private  RadioButton _rb_no;
    private static VMMCLocation _location;



    private static OnFragmentInteractionListener mListener;
    private DBHelper dbHelp;

    /**
     * Use this factory method to create a new instance of
     * this fragment using the provided parameters.
     *
     //* @param param1 Parameter 1.
     //* @param param2 Parameter 2.
     * @return A new instance of fragment EditUserFragment.
     */
    // TODO: Reusername and change types and number of parameters
    //  public static CreateFragment newInstance(String param1, String param2) {
    public static EditUserFragment newInstance(String mEditUserParam, String mEditUserRecordParam) {
        EditUserFragment fragment = new EditUserFragment();
        Bundle bundle = new Bundle();
        bundle.putString("ARG_EDIT_USER_PARAM", mEditUserParam);
        bundle.putString("ARG_EDIT_USER_RECORD_PARAM", mEditUserRecordParam);

        _editUserRecordParam = mEditUserRecordParam;

        fragment.setArguments(bundle);
        return fragment;
    }

    // public EditUserFragment() {
    // Required empty public constructor
    //}

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            _editUserRecordParam = getArguments().getString("ARG_EDIT_USER_RECORD_PARAM");
            Log.d(LOG, "editUserFragment onCreate editUserParam: ");
            Log.d(LOG, "editUserFragment onCreate editUserRecordParam: " + _editUserRecordParam.toString() + "<");
        }

        String params = _editUserRecordParam.toString();
        String parts[] = {};
        parts = params.split(":", 3);

        String _username = "";
        String _phone = "";

        switch (parts.length) {
            case 0: {
                // add
                break;
            }
            case 1: {
                _username = parts[0];
                break;
            }
            case 2: {
                _username = parts[0];
                _phone = parts[1];
                break;
            }

            case 3: {
                _username = parts[0];
                _phone = parts[2];
                break;
            }
        }

        Log.d(LOG, "EG Name: " + _username);
        Log.d(LOG, "EG Phone: " + _phone);

        dbHelp = new DBHelper(getActivity());
        _user = dbHelp.getUser(_username, "%", _phone);

       if (_user == null) {
           _user = new User();
       }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        _view = inflater.inflate(R.layout.fragment_edit_user, container, false);

        getActivity().setTitle(getResources().getString(R.string.editUserTitle));
        if(_user != null) {
            _username = (TextView) _view.findViewById(R.id.username);
            _username.setText(_user.get_username());
            _phone = (TextView) _view.findViewById(R.id.phone);
            _phone.setText(_user.get_phone());
            _password = (TextView) _view.findViewById(R.id.password);
            if (_user.get_password() != null) {
                _password.setText(FILLER_PASSWORD);
            }
            _firstName = (TextView) _view.findViewById(R.id.first_name);
            _firstName.setText(_user.get_first_name());
            _lastName = (TextView) _view.findViewById(R.id.last_name);
            _lastName.setText(_user.get_last_name());
            _email = (TextView) _view.findViewById(R.id.email);
            _email.setText(_user.get_email());


        }

        loadRegionCheck(_view );
        loadIsBlockedRadio(_view );
        loadLocationDropdown(_view );
        loadUserTypeDropdown(_view );




        Button btnUpdate = (Button) _view.findViewById(R.id.btnUpdate);
        btnUpdate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Calendar cal = Calendar.getInstance();
                java.util.Date utilDate = cal.getTime();
                java.sql.Date sqlDate = new java.sql.Date(utilDate.getTime());

                _username = (TextView) _view.findViewById(R.id.username);
                String sUsername = _username.getText().toString();
                _phone = (TextView) _view.findViewById(R.id.phone);
                String sPhone = _phone.getText().toString();
                _password = (TextView) _view.findViewById(R.id.password);
                String sPassword = _password.getText().toString();
                _firstName = (TextView) _view.findViewById(R.id.first_name);
                String sFirstName = _firstName.getText().toString();
                _lastName = (TextView) _view.findViewById(R.id.last_name);
                String sLastName = _lastName.getText().toString();
                _email = (TextView) _view.findViewById(R.id.email);
                String sEmail = _email.getText().toString();

               int iRegionId = _user.get_region_id();
               int iIsBlocked = _user.get_is_blocked();

                boolean passwordChanged = !sPassword.equals(FILLER_PASSWORD);
                if (passwordChanged) {
                    PasswordUtil passwordUtil = new PasswordUtil();
                    sPassword = passwordUtil.secureHashPassword(sPassword);
                }

              Spinner utSpinner = (Spinner) _view.findViewById(R.id.user_type);
//              String sUserType  = utSpinner.getSelectedItem().toString();
              UserType _userType = dbHelp.getUserType( utSpinner.getSelectedItem().toString());

              Spinner lSpinner = (Spinner) _view.findViewById(R.id.vmmclocation);
//              String sLocationText  = lSpinner.getSelectedItem().toString();
              VMMCLocation _location = dbHelp.getLocation( lSpinner.getSelectedItem().toString());

                Log.d(LOG, "UpdateUser button: " +
                        sUsername + ", " + sPhone + " <");

                boolean complete = true;
                if(sUsername.matches("") ) complete = false;

                if(complete) {
                    User lookupUser = dbHelp.getUser(sUsername, "%", sPhone );

                    if (lookupUser != null) {
                        lookupUser.set_username(sUsername);
                        lookupUser.set_phone(sPhone);
                        if (passwordChanged) {
                            lookupUser.set_password(sPassword);
                            if (lookupUser.get_username().equals(MainActivity._username)) {
                                MainActivity._password = sPassword;
                                MainActivity.USER_OBJ = new User(dbHelp, MainActivity._username + ":" + MainActivity._password);
                            }
                        }
                        lookupUser.set_user_type_id(_userType.get_id());
                        lookupUser.set_location_id(_location.get_id());
                        lookupUser.set_first_name(sFirstName);
                        lookupUser.set_last_name(sLastName);
                        lookupUser.set_email(sEmail);
                        lookupUser.set_region_id(iRegionId);
                        lookupUser.set_is_blocked(iIsBlocked);

                        Log.d(LOG, "UpdateUser update:0 " + _username.getText() + ", " + _firstName.getText()  + "<");
                        Log.d(LOG, "UpdateUser update:1 " + sUsername + ", " + iRegionId + ", " + iIsBlocked + "<");
                        Log.d(LOG, "UpdateUser update:2 " + lookupUser.get_username() + ", " + lookupUser.get_first_name() + ", " + lookupUser.get_is_blocked() + "<");
                        if(dbHelp.updateUser(lookupUser))
                            Toast.makeText(getActivity(), "User Updated", Toast.LENGTH_LONG).show();
                    } else {
                        User user = new User();
                        user.set_username(sUsername);
                        user.set_phone(sPhone);
                        user.set_password(sPassword);
                        user.set_user_type_id(_userType.get_id());
                        user.set_location_id(_location.get_id());
                        user.set_first_name(sFirstName);
                        user.set_last_name(sLastName);
                        user.set_email(sEmail);
                        user.set_region_id(iRegionId);
                        user.set_is_blocked(iIsBlocked);

                        Log.d(LOG, "UpdateUser add: " + _username.getText() + ", " +  " <");
                        if(dbHelp.addUser(user))
                            Toast.makeText(getActivity(), "User Saved", Toast.LENGTH_LONG).show();;
                    }
                } else {
                    Toast.makeText(getActivity(), "Must enter username", Toast.LENGTH_LONG).show();
                }
            }
        });

        return _view;
    }

    @Override
    public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {}

    public void onNothingSelected(AdapterView<?> arg0) {}

    // TODO: Reusername method, update argument and hook method into UI event
    public void onListItemPressed(int position) {
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
        Log.d(LOG, "Detach: ");
         _user = null;
    }

    @Override
    public void onResume() {
        super.onResume();
        mListener = null;
        Log.d(LOG, "Resume: ");
//        _first_username = (TextView) _view.findViewById(R.id.first_username); _first_username.setText("");


        if(_user != null) {
//            _userusername = (TextView) _view.findViewById(R.id.userusername);
//            _userusername.setText(_user.get_userusername());

        }
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
        // TODO: Update argument type and username
        public void onFragmentInteraction(int position);

    }

    public void loadRegionCheck(View view ) {
        Log.d(LOG, "loadRegionRadio: ");

        _cb_region1 = (CheckBox) _view.findViewById(R.id.cb_Region1);
        _cb_region2 = (CheckBox) _view.findViewById(R.id.cb_Region2);

        if (_user.get_region_id() == 0 ) {
            _cb_region1.setChecked(false);
            _cb_region2.setChecked(false);
        } else if (_user.get_region_id() == 1){
            _cb_region1.setChecked(true);
            _cb_region2.setChecked(false);
        } else if (_user.get_region_id() == 2){
            _cb_region1.setChecked(false);
            _cb_region2.setChecked(true);
        } else if (_user.get_region_id() == 3){
            _cb_region1.setChecked(true);
            _cb_region2.setChecked(true);
        }

        _cb_region1.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                _cb_region=(CheckBox) _view.findViewById(R.id.cb_Region1);
                if(_cb_region.isChecked()) {
                    _user.set_region_id(_user.get_region_id()+1);
                } else {
                    _user.set_region_id(_user.get_region_id()-1);
                }
                Log.d(LOG, "region: " + _user.get_region_id());
            }
        });

        _cb_region2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                _cb_region=(CheckBox) _view.findViewById(R.id.cb_Region2);
                if(_cb_region.isChecked()) {
                    _user.set_region_id(_user.get_region_id()+2);
                }else {
                    _user.set_region_id(_user.get_region_id()-2);
                }
                Log.d(LOG, "region: " + _user.get_region_id());
            }
        });
    }

    public void loadIsBlockedRadio(final View view ) {
        Log.d(LOG, "loadIsBlockedRadio: ");

        _rg_is_blocked = (RadioGroup) _view.findViewById(R.id.is_blocked_radio_group);
        _rb_yes = (RadioButton) _view.findViewById(R.id.radioButtonYes);
        _rb_no = (RadioButton) _view.findViewById(R.id.radioButtonNo);

        if (_user.get_is_blocked() == 1 ) {
            _rb_no.setChecked(false);
            _rb_yes.setChecked(true);
        } else {
            _rb_no.setChecked(true);
            _rb_yes.setChecked(false);
        }

        _rb_yes.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                int selectedId=_rg_is_blocked.getCheckedRadioButtonId();
                _rb_is_blocked=(RadioButton) view.findViewById(selectedId);
                Log.d(LOG, "yes:_rb_is_blocked: " + _rb_is_blocked.getText() );
                _user.set_is_blocked(1);
            }
        });

        _rb_no.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                int selectedId=_rg_is_blocked.getCheckedRadioButtonId();
                _rb_is_blocked=(RadioButton) view.findViewById(selectedId);
                Log.d(LOG, "no:_rb_is_blocked: " + _rb_is_blocked.getText() );
                _user.set_is_blocked(0);
            }
        });
    }

    public void loadUserTypeDropdown(View view ) {
        Log.d(LOG, "loadUserTypeDropdown: " );

        final Spinner utSpinner = (Spinner) view.findViewById(R.id.user_type);
        final List<String> userTypeNames = dbHelp.getAllUserTypes();

        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), R.layout.simple_spinner_item, userTypeNames);
//        {
//            @Override
//            public boolean isEnabled(int position) {
//                return position != 1;
//            }
//
//            @Override
//            public boolean areAllItemsEnabled() {
//                return false;
//            }

//            @Override
//            public View getDropDownView(int position, View convertView, ViewGroup parent){
//                View v = convertView;
//                if (v == null) {
//                    Context mContext = this.getContext();
//                    LayoutInflater vi = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
//                    v = vi.inflate(R.layout.simple_spinner_item, null);
//                }
////                Spinner spinner = (Spinner) v.findViewById(R.id.status);
//                Log.d(LOG, "loadStatusDropdown:position: " + position + ":" + statusNames.get(position));
//                TextView tv = (TextView) v.findViewById(R.id.spinnerTarget);
////                tv.setText(statusNames.get(position));
//
//                switch (position) {
//                    case 0:
////                        tv.setTextColor(Color.RED);
//                        break;
//                    case 1:
////                        tv.setTextColor(Color.BLUE);
//                        break;
//                    default:
////                        tv.setTextColor(Color.BLACK);
//                        break;
//                }
//                return v;
//            }
//        };
        dataAdapter.setDropDownViewResource(R.layout.simple_spinner_item);
        utSpinner.setAdapter(dataAdapter);
        _userType = dbHelp.getUserType(String.valueOf(_user.get_user_type_id()));
        if (_userType == null) {
           _userType = dbHelp.getUserType("Clerk");
        }
        String compareValue = _userType.get_name();
        if (!compareValue.equals(null)) {
            int spinnerPosition = dataAdapter.getPosition(compareValue);
            utSpinner.setSelection(spinnerPosition);
        }

        utSpinner.setOnItemSelectedListener( new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String userTypeText  = utSpinner.getSelectedItem().toString();
                _userType = dbHelp.getUserType(userTypeText);
//                _userType.set_id(_userType.get_id());
//                Log.d(LOG, "userType: " + _userType.get_id() + _userType.get_username());
//                status_type = dbHelp.getStatusType(statusText);
//                Log.d(LOG, "statusId/Name selected: " + status.get_id() + " " + status.get_username());
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                Log.d(LOG, "spinner nothing selected");
            }
        });
    }

    public void loadLocationDropdown(View view ) {
        Log.d(LOG, "loadLocationDropdown: " );

        final Spinner lSpinner = (Spinner) view.findViewById(R.id.vmmclocation);
        final List<String> locationNames = dbHelp.getAllLocationNames();

        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), R.layout.simple_spinner_item, locationNames);

        dataAdapter.setDropDownViewResource(R.layout.simple_spinner_item);
        lSpinner.setAdapter(dataAdapter);
        _location = dbHelp.getLocation(String.valueOf(_user.get_location_id()));
        if (_location == null) {
            _location = dbHelp.getLocation("1");
        }
        String compareValue = _location.get_name();
        if (!compareValue.equals(null)) {
            int spinnerPosition = dataAdapter.getPosition(compareValue);
            lSpinner.setSelection(spinnerPosition);
        }

        lSpinner.setOnItemSelectedListener( new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String locationText  = lSpinner.getSelectedItem().toString();
                _location = dbHelp.getLocation(locationText);
                _user.set_location_id(_location.get_id());
                Log.d(LOG, "location: " + _location.get_id() + _location.get_name());
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                Log.d(LOG, "spinner nothing selected");
            }
        });
    }

}

