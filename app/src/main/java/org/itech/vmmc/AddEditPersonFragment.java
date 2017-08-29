package org.itech.vmmc;

import android.app.Activity;
import android.app.Fragment;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AbsListView;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListAdapter;
import android.widget.Spinner;

import java.util.List;


/**
 * A simple {@link Fragment} subclass.
 * Activities that contain this fragment must implement the
 * {@link AddEditPersonFragment.OnFragmentInteractionListener} interface
 * to handle interaction events.
 * Use the {@link SearchFragment#newInstance} factory method to
 * create an instance of this fragment.
 */
public class AddEditPersonFragment extends Fragment implements AdapterView.OnItemSelectedListener {
    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    public static String TAG = "addEditPersonTag";
    public static String LOG = "gnr";

    //private static final String ARG_PARAM1 = "param1";
    //private static final String ARG_PARAM2 = "param2";

    // TODO: Rename and change types of parameters
    //private String mParam1;
    //private String mParam2;

    private OnFragmentInteractionListener mListener;
    private AbsListView mListView;

    private ListAdapter mAdapter;
    private DBHelper dbHelp;
    /**
     * Use this factory method to create a new instance of
     * this fragment using the provided parameters.
     *
     //* @param param1 Parameter 1.
     //* @param param2 Parameter 2.
     * @return A new instance of fragment SearchFragment.
     */
    // TODO: Rename and change types and number of parameters
    //  public static SearchFragment newInstance(String param1, String param2) {
    public static AddEditPersonFragment newInstance() {
        AddEditPersonFragment fragment = new AddEditPersonFragment();
        Bundle args = new Bundle();
        //args.putString(ARG_PARAM1, param1);
        //args.putString(ARG_PARAM2, param2);
        fragment.setArguments(args);
        return fragment;
    }

    // public SearchFragment() {
    // Required empty public constructor
    //}

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            //mParam1 = getArguments().getString(ARG_PARAM1);
            //mParam2 = getArguments().getString(ARG_PARAM2);
        }

        dbHelp = new DBHelper(getActivity());

    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_add_edit_person, container, false);

        getActivity().setTitle(getResources().getString(R.string.addEditPersonTitle));

        loadPersonNameDropdown(view);
//        loadAssessmentTypeDropdown(view);
        loadNationalIDDropdown(view);
//        loadFacilityDropdown(view);
        loadPhoneNumberDropdown(view);

        final ClearableAutoCompleteTextView nameDropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.name);
        final ClearableAutoCompleteTextView nationalIdDropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.national_id);
        final ClearableAutoCompleteTextView phoneNumberDropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.phone_number);

        Button btnDisplayPerson = (Button) view.findViewById(R.id.btnDisplayPerson);
        btnDisplayPerson.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                java.util.Calendar cal = java.util.Calendar.getInstance();
                java.util.Date utilDate = cal.getTime();
                java.sql.Date sqlDate = new java.sql.Date(utilDate.getTime());

                Log.d(LOG, "DisplayPerson button: ");

                String paramName = "";
                String paramNationalID = "";
                String paramPhoneNumber = "";
                String paramProjectedDate = "";
                Log.d(LOG, "DisplayPerson button name: " + nameDropdown.getText().toString() + "<");

                if (nameDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "DisplayPerson button name is null: ");
                } else {
                    //int bookingId = new Integer(booking.get_id());
                    //paramBookingId = Integer.toString(bookingId);

                    paramName = nameDropdown.getText().toString();
                    String parts[] = {};
                    parts = paramName.split(", ",3);

                    switch( parts.length)  {
                        case 0: {
                            // add
                            break;
                        }
                        case 1: {
                            paramName = parts[0];
                            break;
                        }
                        case 2: {
                            paramName = parts[0];
                            paramNationalID = parts[1];
                            break;
                        }
                        case 3: {
                            paramName = parts[0];
                            paramNationalID = parts[1];
                            paramPhoneNumber = parts[2];
                            break;
                        }
                    }

                    Log.d(LOG, "DisplayPerson button name/all: " + paramName + paramNationalID + paramPhoneNumber + paramProjectedDate);
                }

                boolean complete = false;
                if (nationalIdDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "DisplayPerson button nationalID is empty: ");
                } else {
                    paramNationalID = nationalIdDropdown.getText().toString();
                    complete = true;
                    Log.d(LOG, "DisplayPerson button nationalID: " + paramNationalID);
                }

                if (phoneNumberDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "DisplayBooking button phoneNumber is empty: ");
                } else {
                    paramPhoneNumber = phoneNumberDropdown.getText().toString();
                    complete = true;
                    Log.d(LOG, "DisplayBooking button phoneNumber: " + paramPhoneNumber);
                }

                Fragment fragment;
                fragment = DisplayFragment.newInstance("displayPerson", paramName + ":" + paramNationalID + ":" + paramPhoneNumber + ":" + paramProjectedDate);
                getFragmentManager().beginTransaction().replace(R.id.container, fragment, DisplayFragment.TAG).addToBackStack("DisplayPerson").commit();

//                if (complete && !paramProjectedDate.toString().equals("") ||
//                        !paramName.toString().equals("") && !paramProjectedDate.toString().equals("") ) {
//
//                    Fragment fragment;
//                    fragment = DisplayFragment.newInstance("displayBooking", paramName + ":" + paramNationalID + ":" + paramPhoneNumber + ":" + paramProjectedDate);
//                    getFragmentManager().beginTransaction().replace(R.id.container, fragment, DisplayFragment.TAG).addToBackStack("DisplayBooking").commit();
//                } else {
//                    Toast.makeText(getActivity(), "Must enter Name or ID or Phone and Date", Toast.LENGTH_LONG).show();
//                }
            }
        });


        Button btnEditPerson = (Button) view.findViewById(R.id.btnEditPerson);
        btnEditPerson.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                java.util.Calendar cal = java.util.Calendar.getInstance();
                java.util.Date utilDate = cal.getTime();
                java.sql.Date sqlDate = new java.sql.Date(utilDate.getTime());

                Log.d(LOG, "EditPerson button: ");

                String paramName = "";
                String paramNationalID = "";
                String paramPhoneNumber = "";
                Log.d(LOG, "EditPerson button name: " + nameDropdown.getText().toString() + "<");

                if (nameDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditPerson button name is null: ");
                } else {
                    //int personId = new Integer(person.get_id());
                    //paramPersonId = Integer.toString(personId);

                    paramName = nameDropdown.getText().toString();
                    String parts[] = {};
                    parts = paramName.split(", ",3);

                    switch( parts.length)  {
                        case 0: {
                            // add
                            break;
                        }
                        case 1: {
                            paramName = parts[0];
                            break;
                        }
                        case 2: {
                            paramName = parts[0];
                            paramNationalID = parts[1];
                            break;
                        }
                        case 3: {
                            paramName = parts[0];
                            paramNationalID = parts[1];
                            paramPhoneNumber = parts[2];
                            break;
                        }
                    }

                    Log.d(LOG, "EditPerson button name/all: " + paramName + paramNationalID + paramPhoneNumber);
                }

                if (nationalIdDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditPerson button nationalID is empty: ");
                } else {
                    paramNationalID = nationalIdDropdown.getText().toString();
                    Log.d(LOG, "EditPerson button nationalID: " + paramNationalID);
                }

                if (phoneNumberDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditPerson button phoneNumber is empty: ");
                } else {
                    paramPhoneNumber = phoneNumberDropdown.getText().toString();
                    Log.d(LOG, "EditPerson button phoneNumber: " + paramPhoneNumber);
                }

                Fragment fragment;
                fragment = EditPersonFragment.newInstance("editPerson", paramName + ":" + paramNationalID + ":" + paramPhoneNumber);
                getFragmentManager().beginTransaction().replace(R.id.container, fragment, EditPersonFragment.TAG).addToBackStack("EditPerson").commit();
            }
        });

//        Button btnAddPerson = (Button) view.findViewById(R.id.btnAddPerson);
//        btnAddPerson.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//
//                java.util.Calendar cal = java.util.Calendar.getInstance();
//                java.util.Date utilDate = cal.getTime();
//                java.sql.Date sqlDate = new java.sql.Date(utilDate.getTime());
//
//                Log.d(LOG, "AddPerson button: ");
//
//                String paramPersonId = "";
//                String paramNationalID = "";
//                String paramPhoneNumber = "";
//                Log.d(LOG, "AddPerson button name: " + nameDropdown.getText().toString() + "<");
//
//                if (person == null || nameDropdown.getText().toString().equals("")) {
//                    Log.d(LOG, "AddPerson button name is null: ");
//                } else {
//                    Log.d(LOG, "AddPerson button name: " + nameDropdown.getText().toString());
//                    int personId = new Integer(person.get_id());
//                    paramPersonId = Integer.toString(personId);
//                }
//                if (nationalIdDropdown.equals("")) {
//                    Log.d(LOG, "AddPerson button nationalID is null: ");
//                } else {
//                    Log.d(LOG, "AddPerson button nationalID: " + nationalIdDropdown.getText());
//                    paramNationalID = nationalIdDropdown.getText().toString();
//                }
//
//                if (phoneNumberDropdown.equals("")) {
//                    Log.d(LOG, "AddPerson button phoneNumber is null: ");
//                } else {
//                    Log.d(LOG, "AddPerson button phoneNumber: " + phoneNumberDropdown.getText());
//                    paramPhoneNumber = phoneNumberDropdown.getText().toString();
//                }
//
//                // get ptoa's where params and goto list
//                //List<String> searchAssessments = dbHelp.getReadablAssessments(paramPersonId, nationalID, facilityName, paramAssessmentType, from_date, to_date);
//
//                Fragment fragment;
//                //fragment = getFragmentManager().findFragmentByTag(RecentFragment.TAG);
//                //if (fragment == null) {
//                fragment = AddPersonFragment.newInstance("addPerson", paramPersonId + ":" + paramNationalID + ":" + paramPhoneNumber + ":");
//                //}
//                getFragmentManager().beginTransaction().replace(R.id.container, fragment, AddPersonFragment.TAG).addToBackStack("AddPerson").commit();
//            }
//        });

        // Inflate the layout for this fragment
        return view;
    }

    @Override
    public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {}

    public void onNothingSelected(AdapterView<?> arg0) {}

    // TODO: Rename method, update argument and hook method into UI event
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
        void onFragmentInteraction(int position);

    }

    public void onResume() {
        super.onResume();
        Log.d(LOG, "person fragment:onResume: pop " );

        getView().setFocusableInTouchMode(true);
        getView().requestFocus();
        getView().setOnKeyListener(new View.OnKeyListener() {
            @Override
            public boolean onKey(View v, int keyCode, KeyEvent event) {
                if (event.getAction() == KeyEvent.ACTION_UP && keyCode == KeyEvent.KEYCODE_BACK){
                    Log.d(LOG, "person fragment:onResume: pop: handle back " );
                    getFragmentManager().popBackStack();
                    Fragment fragment = getFragmentManager().findFragmentByTag(ActionFragment.TAG);
                    if (fragment == null) {
                        fragment = ActionFragment.newInstance("main", "");
                        getFragmentManager().beginTransaction().replace(R.id.container, fragment, ActionFragment.TAG).addToBackStack(MainActivity.currentFragmentId).commit();
                    } else {
                        getFragmentManager().beginTransaction().replace(R.id.container, fragment, ActionFragment.TAG).commit();
                    }
                    MainActivity.currentFragmentId = "Action";
                    return true;
                }
                return false;
            }
        });
    }

    private Person person;
    public void loadPersonNameDropdown(View view) {

        List<String> personIDs = dbHelp.getAllPersonIDs();
        // convert to array
        String[] stringArrayPersonID = new String[ personIDs.size() ];
        personIDs.toArray(stringArrayPersonID);

        final ClearableAutoCompleteTextView dropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.name);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, stringArrayPersonID);
        dropdown.setThreshold(1);
        dropdown.setAdapter(dataAdapter);

        dropdown.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            public void onItemClick(AdapterView<?> parent, View view, int index, long position) {
                String nameText = dropdown.getText().toString();
                String parts[] = {};
                parts = nameText.split(", ");

                String name = parts[0].trim();
                String national_id =  parts[1].trim();
                String phone_number = parts[2].trim();
                Log.d(LOG, "person selected: " + name + "." + national_id + "." + phone_number + ".");

                //person = dbHelp.getPerson(first_name, last_name, national_id, facility_name);
//                Log.d(LOG, "person_id selected: " + person.get_person_id());
//                Log.d(LOG, "first_name selected: " + first_name);
//                Log.d(LOG, "last_name selected: " + last_name);
//                Log.d(LOG, "national_id selected: " + national_id);
//                Log.d(LOG, "facility_name selected: " + facility_name);
            }
        });
    }

    public void loadNationalIDDropdown(View view) {
        String[] nationalIDs = dbHelp.getAllNationalIDs();

        final ClearableAutoCompleteTextView dropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.national_id);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, nationalIDs);
        dropdown.setThreshold(1);
        dropdown.setAdapter(dataAdapter);

        dropdown.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            public void onItemClick(AdapterView<?> parent, View view, int index, long position) {
                Log.d(LOG, "nationalID selected: " + dropdown.getText());
            }
        });
    }

    public void loadFacilityDropdown(View view) {
        String[] facilityNames = dbHelp.getAllFacilityNames();

        final ClearableAutoCompleteTextView dropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.last_name);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, facilityNames);
        dropdown.setThreshold(1);
        dropdown.setAdapter(dataAdapter);

        dropdown.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            public void onItemClick(AdapterView<?> parent, View view, int index, long position) {
                Log.d(LOG, "facility selected: " + dropdown.getText());
            }
        });

    }

    public void loadPhoneNumberDropdown(View view) {
        String[] phoneNumbers = dbHelp.getAllPhoneNumbers();

        final ClearableAutoCompleteTextView dropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.phone_number);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, phoneNumbers);
        dropdown.setThreshold(1);
        dropdown.setAdapter(dataAdapter);
        // dropdown.setTextSize(R.dimen.font_size_medium);
        //dropdown.setTextSize(TypedValue.COMPLEX_UNIT_DIP,25);

        dropdown.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            public void onItemClick(AdapterView<?> parent, View view, int index, long position) {
                Log.d(LOG, "phoneNumber selected: " + dropdown.getText());
            }
        });

    }

    private Assessments assessment = null;
    public void loadAssessmentTypeDropdown(View view) {
        final Spinner dropdown = (Spinner) view.findViewById(R.id.assessment_type);
        dropdown.setOnItemSelectedListener( new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                String assessmentTypeText  = dropdown.getSelectedItem().toString();
                Log.d(LOG, "assessmentTypeText: " + assessmentTypeText + "<");
                // because of the all option, not available in create
                if(!assessmentTypeText.equals("")) {
                    assessment = dbHelp.getAssessments(assessmentTypeText);
                } else assessment = null;
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                Log.d(LOG, "spinner nothing selected");
            }
        });

        List<String> assessmentTypes = dbHelp.getAllAssessmentTypes();
        String all = "";
        assessmentTypes.add(0,all);

        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, assessmentTypes);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        dropdown.setAdapter(dataAdapter);

    }

}
