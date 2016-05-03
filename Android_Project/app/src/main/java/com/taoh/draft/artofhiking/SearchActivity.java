package com.taoh.draft.artofhiking;

import android.content.Intent;
import android.graphics.drawable.BitmapDrawable;
import android.os.AsyncTask;
import android.provider.MediaStore;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.SparseBooleanArray;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.SeekBar;
import android.widget.TextView;

import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Polyline;
import com.google.android.gms.maps.model.PolylineOptions;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;

public class SearchActivity extends AppCompatActivity {
    private double tlmax;
    private double tlmin;
    private double tlrange;
    private double egmax;
    private double egmin;
    private double egrange;
    private double currentTLMin;
    private double currentTLMax;
    private double currentEGMin;
    private double currentEGMax;
    private int ctlmax;
    private int ctlmin;
    private int cegmax;
    private int cegmin;
    private ListView lvRegions;
    private ListView lvIPs;
    private SeekBar sbMaxTL;
    private SeekBar sbMaxEG;
    private SeekBar sbMinTL;
    private SeekBar sbMinEG;
    private TextView txtMinEG;
    private TextView txtMaxEG;
    private TextView txtMinTL;
    private TextView txtMaxTL;
    private HashMap<String, Integer> regionHM = new HashMap<>();
    private HashMap<String, Integer> ipHM = new HashMap<>();
    private RadioGroup rgHA;
    private HashMap<Integer, String> idStringHM = new HashMap<>();
    private HashMap<String, Integer> haIdHM = new HashMap<>();
    private String horse = "default";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search);

        lvRegions = (ListView)findViewById(R.id.regionList);
        lvIPs = (ListView)findViewById(R.id.interestPointsList);

        rgHA = (RadioGroup)findViewById(R.id.HorseAccessibleRG);
        idStringHM.put(R.id.rbTrue, "true");
        idStringHM.put(R.id.rbFalse, "false");
        idStringHM.put(R.id.rbDefault, "default");
        haIdHM.put("true", R.id.rbTrue);
        haIdHM.put("false", R.id.rbFalse);
        haIdHM.put("default", R.id.rbDefault);

        txtMaxEG = (TextView)findViewById(R.id.txtMaxEG);
        txtMaxTL = (TextView)findViewById(R.id.txtMaxTL);
        txtMinEG = (TextView)findViewById(R.id.txtMinEG);
        txtMinTL = (TextView)findViewById(R.id.txtMinTL);

        sbMaxEG = (SeekBar)findViewById(R.id.maxElevationGainSeekBar);
        sbMaxTL = (SeekBar)findViewById(R.id.maxTrailLengthSeekBar);
        sbMinEG = (SeekBar)findViewById(R.id.minElevationGainSeekBar);
        sbMinTL = (SeekBar)findViewById(R.id.minTrailLengthSeekBar);
        sbMaxEG.setMax(100);
        sbMaxEG.setProgress(100);
        cegmax = 100;
        sbMaxTL.setMax(100);
        sbMaxTL.setProgress(100);
        ctlmax = 100;
        sbMinEG.setMax(100);
        sbMinEG.setProgress(0);
        cegmin = 0;
        sbMinTL.setMax(100);
        sbMinTL.setProgress(0);
        ctlmin = 0;
        sbMaxEG.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            @Override
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                if (progress < cegmin) {
                    seekBar.setProgress(cegmin);
                    cegmax = cegmin;
                } else {
                    cegmax = progress;
                }
                double prg = (((double)cegmax)/100.0) * egrange + egmin;
                currentEGMax = prg;
                txtMaxEG.setText("Max Elevation Gain: " + String.format("%.2f",prg) + " miles");
            }

            @Override
            public void onStartTrackingTouch(SeekBar seekBar) {

            }

            @Override
            public void onStopTrackingTouch(SeekBar seekBar) {

            }
        });
        sbMinEG.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            @Override
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                cegmin = progress;
                if (progress > cegmax) {
                    sbMaxEG.setProgress(progress);
                }
                double prg = (((double)progress)/100.0) * egrange + egmin;
                currentEGMin = prg;
                txtMinEG.setText("Min Elevation Gain: " + String.format("%.2f",prg) + " miles");
            }

            @Override
            public void onStartTrackingTouch(SeekBar seekBar) {

            }

            @Override
            public void onStopTrackingTouch(SeekBar seekBar) {

            }
        });
        sbMaxTL.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            @Override
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                System.out.println(progress);
                if (progress < ctlmin) {
                    seekBar.setProgress(ctlmin);
                    ctlmax = ctlmin;
                } else {
                    ctlmax = progress;
                }
                double prg = (((double)ctlmax)/100.0) * tlrange + tlmin;
                currentTLMax = prg;
                txtMaxTL.setText("Max Trail Length: " + String.format("%.2f",prg) + " miles");
            }

            @Override
            public void onStartTrackingTouch(SeekBar seekBar) {

            }

            @Override
            public void onStopTrackingTouch(SeekBar seekBar) {

            }
        });
        sbMinTL.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            @Override
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                System.out.println(progress);
                ctlmin = progress;
                if (progress > ctlmax) {
                    sbMaxTL.setProgress(progress);
                }
                double prg = (((double) progress) / 100.0) * tlrange + tlmin;
                currentTLMin = prg;
                txtMinTL.setText("Min Trail Length: " + String.format("%.2f",prg) + " miles");
            }

            @Override
            public void onStartTrackingTouch(SeekBar seekBar) {

            }

            @Override
            public void onStopTrackingTouch(SeekBar seekBar) {

            }
        });

        rgHA.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                horse = idStringHM.get(checkedId);
            }
        });

        if(FilterData.getInstance().isSet())
        {
            setAdapFromFilterData();
        } else {
            new CallApi().execute();
        }
    }

    @Override
    protected void onDestroy() {
        lvRegions = null;
        lvIPs = null;
        sbMaxTL = null;
        sbMaxEG = null;
        System.out.println("Search Destroyed");
        super.onDestroy();
        unbindDrawables(findViewById(R.id.searchLayout));
        System.gc();
    }

    static void unbindDrawables(View view) {
        try{
            if (view.getBackground() != null) {
                System.out.println(view.toString());
                ((BitmapDrawable) view.getBackground()).getBitmap().recycle();
                view.getBackground().setCallback(null);
                view=null;
            }
            if (view instanceof ViewGroup) {
                for (int i = 0; i < ((ViewGroup) view).getChildCount(); i++) {
                    unbindDrawables(((ViewGroup) view).getChildAt(i));
                }
                ((ViewGroup) view).removeAllViews();
            }

        }catch (Exception e) {
            System.err.println(e.toString());
        }
    }


    public class CallApi extends AsyncTask<String, Void, JSONObject> {
        protected JSONObject doInBackground(String... urls) {
            try {
                final String USER_AGENT = "Mozilla/5.0";
                final String POST_URL = "https://web.eecs.utk.edu/~ghanas/service.php";
                String POST_PARAMS = "request=getFilters&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";

                URL obj = new URL(POST_URL);
                HttpURLConnection con = (HttpURLConnection) obj.openConnection();
                con.setRequestMethod("POST");
                con.setRequestProperty("User-Agent", USER_AGENT);

                con.setDoOutput(true);
                OutputStream os = con.getOutputStream();

                os.write(POST_PARAMS.getBytes());

                os.flush();
                os.close();

                int responseCode = con.getResponseCode();
                System.out.println("POST Response Code :: " + responseCode);

                if (responseCode == HttpURLConnection.HTTP_OK) { //success
                    BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));
                    String inputLine;
                    StringBuffer response = new StringBuffer();

                    while ((inputLine = in.readLine()) != null) {
                        response.append(inputLine);
                    }
                    in.close();
                    System.out.println("Response: " + response.toString());
                    return new JSONObject(response.toString());
                }
            }
            catch (Exception e){
                System.err.println(e.toString());
                System.err.println(e.getMessage());
                for (int i = 0; i < e.getStackTrace().length; i++)
                    System.err.println(e.getStackTrace()[i].toString());
            }

            return null;
        }
        protected void onPostExecute(JSONObject result)
        {
            if(result == null) return;
            try{

                System.out.println(result.toString());
                JSONObject jo = result.getJSONObject("regions");
                System.out.println(jo.toString());
                System.out.println(jo.keys().toString());
                Iterator<String> it = jo.keys();
                while(it.hasNext())
                {
                    String key = it.next();
                    int regionid = Integer.parseInt(key);
                    String value = jo.getString(key);
                    System.out.println("Key = " + key + " Value = " + value);
                    regionHM.put(value, regionid);
                }
                jo = result.getJSONObject("interestPoints");
                System.out.println(jo.toString());
                System.out.println(jo.keys().toString());
                it = jo.keys();
                while(it.hasNext())
                {
                    String key = it.next();
                    int ipid = Integer.parseInt(key);
                    String value = jo.getString(key);
                    System.out.println("Key = " + key + " Value = " + value);
                    ipHM.put(value, ipid);
                }

                tlmax = result.getDouble("maxTrailLen");
                tlmin = result.getDouble("minTrailLen");
                egmax = result.getDouble("maxElevationGain");
                egmin = result.getDouble("minElevationGain");
                currentEGMax = egmax;
                currentEGMin = egmin;
                currentTLMax = tlmax;
                currentTLMin = tlmin;
                tlrange = tlmax - tlmin;
                egrange = egmax - egmin;
                setAdap();

            }
            catch (Exception e){
                System.err.println(e.toString());
                System.err.println(e.getMessage());
                for (int i = 0; i < e.getStackTrace().length; i++)
                    System.err.println(e.getStackTrace()[i].toString());
            }
        }
    }

    public void setAdap() {
        String[] regions = (String[])regionHM.keySet().toArray(new String[0]);
        String[] ips = (String[])ipHM.keySet().toArray(new String[0]);

        ArrayAdapter<String> regionsAdapter =
                new ArrayAdapter<>(this, android.R.layout.simple_list_item_multiple_choice, android.R.id.text1, regions);

        ArrayAdapter<String> ipsAdapter =
                new ArrayAdapter<>(this, android.R.layout.simple_list_item_multiple_choice, android.R.id.text1, ips);

        lvRegions.setAdapter(regionsAdapter);
        lvIPs.setAdapter(ipsAdapter);
        setFilterData();
    }

    private void setFilterData()
    {
        SparseBooleanArray selR = lvRegions.getCheckedItemPositions();
        SparseBooleanArray selI = lvIPs.getCheckedItemPositions();
        ArrayList<Integer> selRegions = new ArrayList<>();
        ArrayList<Integer> selIPs = new ArrayList<>();
        for(int i = 0; i < selR.size(); i++)
        {
            if(selR.valueAt(i))
            {
                selRegions.add(regionHM.get(lvRegions.getAdapter().getItem(i)));
            }
        }
        for(int i = 0; i < selI.size(); i++)
        {
            if(selI.valueAt(i))
            {
                selIPs.add(ipHM.get(lvIPs.getAdapter().getItem(i)));
            }
        }
        System.out.println(selIPs.toString());
        FilterData.getInstance().setValues(tlmin, egmin, tlmax, egmax, currentTLMin, currentEGMin, currentTLMax, currentEGMax, regionHM, ipHM, selRegions, selIPs, selR, selI,horse);
    }

    public void setAdapFromFilterData()
    {

        regionHM = FilterData.getInstance().regionHM;
        ipHM = FilterData.getInstance().interestPointsHM;
        currentEGMax = FilterData.getInstance().selMaxElevationGain;
        currentEGMin = FilterData.getInstance().selMinElevationGain;
        currentTLMax = FilterData.getInstance().selMaxTrailLength;
        currentTLMin = FilterData.getInstance().selMinTrailLength;
        egmax = FilterData.getInstance().maxElevationGain;
        egmin = FilterData.getInstance().minElevationGain;
        tlmax = FilterData.getInstance().maxTrailLength;
        tlmin = FilterData.getInstance().minTrailLength;
        horse = FilterData.getInstance().horseAccessibility;
        egrange = egmax - egmin;
        tlrange = tlmax - tlmin;
        cegmax = (int)(((currentEGMax - egmin)/egrange)*100 );
        cegmin = (int)(((currentEGMin - egmin)/egrange)*100 );
        ctlmax = (int)(((currentTLMax - tlmin)/tlrange)*100 );
        ctlmin = (int)(((currentTLMin - tlmin)/tlrange)*100 );

        sbMaxEG.setProgress(cegmax);
        sbMinEG.setProgress(cegmin);
        sbMaxTL.setProgress(ctlmax);
        sbMinTL.setProgress(ctlmin);

        rgHA.check(haIdHM.get(horse));
        System.out.println("Reload int" + haIdHM.get(horse));
        System.out.println("False: " + R.id.rbFalse + " True: " + R.id.rbTrue + " Default: " + R.id.rbDefault);

        String[] regions = (String[])regionHM.keySet().toArray(new String[0]);
        String[] ips = (String[])ipHM.keySet().toArray(new String[0]);

        ArrayAdapter<String> regionsAdapter =
                new ArrayAdapter<>(this, android.R.layout.simple_list_item_multiple_choice, android.R.id.text1, regions);

        ArrayAdapter<String> ipsAdapter =
                new ArrayAdapter<>(this, android.R.layout.simple_list_item_multiple_choice, android.R.id.text1, ips);

        lvRegions.setAdapter(regionsAdapter);
        lvIPs.setAdapter(ipsAdapter);

        SparseBooleanArray selR = FilterData.getInstance().sRegions;
        SparseBooleanArray selI = FilterData.getInstance().sIPs;

        for(int i = 0; i < selR.size(); i++)
        {
            if(selR.valueAt(i))
            {
                lvRegions.setItemChecked(i, true);
            }
        }
        for(int i = 0; i < selI.size(); i++)
        {
            if(selI.valueAt(i))
            {
                lvIPs.setItemChecked(i, true);
            }
        }

    }

    public void goToList(View v)
    {
        setFilterData();
        Intent intent = new Intent(this, ListViewActivity.class);
        startActivity(intent);
        finish();
    }

    public void goToMain(View v)
    {
        setFilterData();
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);
        finish();
    }

}