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
import android.widget.Toast;

import java.util.Calendar;
import java.util.List;

import static org.itech.vmmc.R.id.btnEdit;


/**
 * A simple {@link Fragment} subclass.
 * Activities that contain this fragment must implement the
 * {@link AddEditFacilitatorFragment.OnFragmentInteractionListener} interface
 * to handle interaction events.
 * Use the {@link AddEditFacilitatorFragment#newInstance} factory method to
 * create an instance of this fragment.
 */
public class AddEditFacilitatorFragment extends Fragment implements AdapterView.OnItemSelectedListener {
    // TODO: Rename parameter arguments, choose names that match
    // the fragment initialization parameters, e.g. ARG_ITEM_NUMBER
    public static String TAG = "addEditFacilitatorTag";
    public static String LOG = "gnr";

    //private static final String ARG_PARAM1 = "param1";
    //private static final String ARG_PARAM2 = "param2";

    // TODO: Rename and change types of parameters
    //private String mParam1;
    //private String mParam2;

    private OnFragmentInteractionListener mListener;
    private AbsListView mListView;

//    private EditText et_projected_date;

    private ListAdapter mAdapter;
    private DBHelper dbHelp;
    /**
     * Use this factory method to create a new instance of
     * this fragment using the provided parameters.
     *
     //* @param param1 Parameter 1.
     //* @param param2 Parameter 2.
     * @return A new instance of fragment AddEditFacilitatorFragment.
     */
    // TODO: Rename and change types and number of parameters
    //  public static AddEditFacilitatorFragment newInstance(String param1, String param2) {
    public static AddEditFacilitatorFragment newInstance() {
        AddEditFacilitatorFragment fragment = new AddEditFacilitatorFragment();
        Bundle args = new Bundle();
        //args.putString(ARG_PARAM1, param1);
        //args.putString(ARG_PARAM2, param2);
        fragment.setArguments(args);
        return fragment;
    }

    // public AddEditFacilitatorFragment() {
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
        View view = inflater.inflate(R.layout.fragment_add_edit_facilitator, container, false);

        getActivity().setTitle(getResources().getString(R.string.addEditFacilitatorTitle));

        loadFacilitatorNameDropdown(view);
//        loadAssessmentTypeDropdown(view);
//        loadNationalIDDropdown(view);
//        loadFacilityDropdown(view);
//        loadPhoneNumberDropdown(view);

        final ClearableAutoCompleteTextView nameDropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.name);
        final ClearableAutoCompleteTextView nationalIdDropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.national_id);
        final ClearableAutoCompleteTextView phoneNumberDropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.phone_number);
//        final EditText projectedDate = (EditText) view.findViewById(R.id.projected_date);

//        et_projected_date = (EditText) view.findViewById(R.id.projected_date);
//        final SimpleDateFormat dateFormatter = new SimpleDateFormat(dbHelp.VMMC_DATE_FORMAT);
//        Calendar newCalendar = Calendar.getInstance();
//        DatePickerDialog hold_projected_date_picker_dialog = new DatePickerDialog(getActivity(), new DatePickerDialog.OnDateSetListener() {
//            public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
//                Calendar newDate = Calendar.getInstance();
//                newDate.set(year, monthOfYear, dayOfMonth);
//                et_projected_date.setText(dateFormatter.format(newDate.getTime()));
//                Log.d(LOG, "AEBF: onDateSet: " + et_projected_date.getText());
//            }
//        }, newCalendar.get(Calendar.YEAR), newCalendar.get(Calendar.MONTH), newCalendar.get(Calendar.DAY_OF_MONTH));
//
//        final DatePickerDialog projected_date_picker_dialog = hold_projected_date_picker_dialog;
//
//        et_projected_date.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                Log.d(LOG, "onClick: ");
//                projected_date_picker_dialog.show();
//            }
//        });

        Button btnDisplayFacilitator = (Button) view.findViewById(R.id.btnDisplay);
        btnDisplayFacilitator.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Calendar cal = Calendar.getInstance();
                java.util.Date utilDate = cal.getTime();
                java.sql.Date sqlDate = new java.sql.Date(utilDate.getTime());

                Log.d(LOG, "DisplayFacilitator button: ");

                String paramName = "";
                String paramNationalID = "";
                String paramPhoneNumber = "";
                String paramProjectedDate = "";
                Log.d(LOG, "EditFacilitator button name: " + nameDropdown.getText().toString() + "<");

                if (nameDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditFacilitator button name is null: ");
                } else {
                    //int FacilitatorId = new Integer(Facilitator.get_id());
                    //paramFacilitatorId = Integer.toString(FacilitatorId);

                    paramName = nameDropdown.getText().toString();
                    String parts[] = {};
                    parts = paramName.split(", ",4);

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
//                        case 4: {
//                            paramName = parts[0];
//                            paramNationalID = parts[1];
//                            paramPhoneNumber = parts[2];
//                            paramProjectedDate = parts[3];
//                            break;
//                        }
                    }

                    Log.d(LOG, "DisplayFacilitator button name/all: " + paramName + paramNationalID + paramPhoneNumber + paramProjectedDate);
                }

                boolean complete = false;
                if (nationalIdDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "DisplayFacilitator button nationalID is empty: ");
                } else {
                    paramNationalID = nationalIdDropdown.getText().toString();
                    complete = true;
                    Log.d(LOG, "DisplayFacilitator button nationalID: " + paramNationalID);
                }

                if (phoneNumberDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "DisplayFacilitator button phoneNumber is empty: ");
                } else {
                    paramPhoneNumber = phoneNumberDropdown.getText().toString();
                    complete = true;
                    Log.d(LOG, "DisplayFacilitator button phoneNumber: " + paramPhoneNumber);
                }

                Fragment fragment;
                fragment = DisplayFragment.newInstance("displayFacilitator", paramName + ":" + paramNationalID + ":" + paramPhoneNumber + ":" + paramProjectedDate);
                getFragmentManager().beginTransaction().replace(R.id.container, fragment, DisplayFragment.TAG).addToBackStack("DisplayFacilitator").commit();
            }
        });

        Button btnEditFacilitator = (Button) view.findViewById(btnEdit);
        btnEditFacilitator.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Calendar cal = Calendar.getInstance();
                java.util.Date utilDate = cal.getTime();
                java.sql.Date sqlDate = new java.sql.Date(utilDate.getTime());

                Log.d(LOG, "EditFacilitator button: ");

                String paramName = "";
                String paramNationalID = "";
                String paramPhoneNumber = "";
                String paramProjectedDate = "";
                Log.d(LOG, "EditFacilitator button name: " + nameDropdown.getText().toString() + "<");

                if (nameDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditFacilitator button name is null: ");
                } else {
                    //int FacilitatorId = new Integer(Facilitator.get_id());
                    //paramFacilitatorId = Integer.toString(FacilitatorId);

                    paramName = nameDropdown.getText().toString();
                    String parts[] = {};
                    parts = paramName.split(", ",4);

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
                        case 4: {
                            paramName = parts[0];
                            paramNationalID = parts[1];
                            paramPhoneNumber = parts[2];
                            paramProjectedDate = parts[3];
                            break;
                        }
                    }

                    Log.d(LOG, "EditFacilitator button name/all: " + paramName + paramNationalID + paramPhoneNumber + paramProjectedDate);
                }

                boolean complete = false;
                if (nationalIdDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditFacilitator button nationalID is empty: ");
                } else {
                    paramNationalID = nationalIdDropdown.getText().toString();
                    complete = true;
                    Log.d(LOG, "EditFacilitator button nationalID: " + paramNationalID);
                }

                if (phoneNumberDropdown.getText().toString().equals("")) {
                    Log.d(LOG, "EditFacilitator button phoneNumber is empty: ");
                } else {
                    paramPhoneNumber = phoneNumberDropdown.getText().toString();
                    complete = true;
                    Log.d(LOG, "EditFacilitator button phoneNumber: " + paramPhoneNumber);
                }

                if (true || !paramName.toString().equals("")  ) {
                    Fragment fragment;
                    fragment = EditFacilitatorFragment.newInstance("editFacilitator", paramName + ":" + paramNationalID + ":" + paramPhoneNumber + ":" + paramProjectedDate);
                    getFragmentManager().beginTransaction().replace(R.id.container, fragment, EditFacilitatorFragment.TAG).addToBackStack("EditFacilitator").commit();
                } else {
                    Toast.makeText(getActivity(), "Must enter Name or ID or Phone", Toast.LENGTH_LONG).show();
                }
            }
        });

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
        public void onFragmentInteraction(int position);

    }

    public void onResume() {
        super.onResume();
        Log.d(LOG, "facilitator fragment:onResume: pop " );

        getView().setFocusableInTouchMode(true);
        getView().requestFocus();
        getView().setOnKeyListener(new View.OnKeyListener() {
            @Override
            public boolean onKey(View v, int keyCode, KeyEvent event) {
                if (event.getAction() == KeyEvent.ACTION_UP && keyCode == KeyEvent.KEYCODE_BACK){
                    Log.d(LOG, "facilitator fragment:onResume: pop: handle back " );
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

    private Facilitator Facilitator;
    public void loadFacilitatorNameDropdown(View view) {

//        List<String> FacilitatorIDs = dbHelp.getAllPersonIDs();
        List<String> FacilitatorIDs = dbHelp.getAllFacilitatorIDs();
        // convert to array
        String[] stringArrayFacilitatorID = new String[ FacilitatorIDs.size() ];
        FacilitatorIDs.toArray(stringArrayFacilitatorID);

        final ClearableAutoCompleteTextView dropdown = (ClearableAutoCompleteTextView) view.findViewById(R.id.name);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, stringArrayFacilitatorID);
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
//                String projected_date = parts[3].trim();
                Log.d(LOG, "Facilitator client selected: " + name + "." + national_id + "." + phone_number + "." );

//                Facilitator = dbHelp.getFacilitator(national_id, phone_number, projected_date);
//                Log.d(LOG, "Facilitator_id selected: " + Facilitator.get_id());

            }
        });
    }

    public void loadNationalIDDropdown(View view) {
        String[] nationalIDs = dbHelp.getAllPersonNationalIDs();

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

    public void loadPhoneNumberDropdown(View view) {
        String[] phoneNumbers = dbHelp.getAllPersonPhoneNumbers();

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

}
