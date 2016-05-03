package com.taoh.draft.artofhiking;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.BitmapDrawable;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;

import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
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
import java.util.List;

public class MainActivity extends AppCompatActivity implements OnMapReadyCallback {
    private HashMap<Polyline, String> hashMap = new HashMap<Polyline,String>();
    private HashMap<String,Integer> nameToId = new HashMap<String,Integer>();
    private GoogleMap mMap;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        SupportMapFragment mf = ((SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.mapFragment));
        if(mf != null) {
            mf.getMapAsync(this);
        } else {
            System.err.println("MapFragment null - there was an error...");
        }
    }

    @Override
    protected void onDestroy() {
        mMap = null;
        hashMap = null;
        System.out.println("Main Destroyed");
        super.onDestroy();
        unbindDrawables(findViewById(R.id.mainLayout));
        System.gc();
    }

    static void unbindDrawables(View view) {
        try{
            if (view.getBackground() != null) {
                ((BitmapDrawable)view.getBackground()).getBitmap().recycle();
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
            System.err.println(e.getMessage());
            for (int i = 0; i < e.getStackTrace().length; i++)
                System.err.println(e.getStackTrace()[i].toString());
        }
    }

    @Override
    public void onMapReady(GoogleMap map) {
        mMap = map;
        mMap.setOnPolylineClickListener(new GoogleMap.OnPolylineClickListener() {
            @Override
            public void onPolylineClick(Polyline polyline) {
                polyclick(polyline);
            }
        });
        new CallApi().execute();
    }

    public class CallApi extends AsyncTask<String, Void, JSONArray> {
        protected JSONArray doInBackground(String... urls) {
            String responseStr = null;
            try {
                String params = FilterData.getInstance().getQueryString();
                final String USER_AGENT = "Mozilla/5.0";
                final String POST_URL = "https://web.eecs.utk.edu/~ghanas/service.php";
                String POST_PARAMS = "request=getTrailPoints&" + FilterData.getInstance().getQueryString() + "&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
                System.out.println("Res: " + POST_PARAMS);

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
                    //JSONArray arr = new JSONArray();
                    List<JSONObject> arr = new ArrayList<>();

                    while ((inputLine = in.readLine()) != null) {
                        //response.append(inputLine);
                        arr.add(new JSONObject(inputLine));
                    }
                    in.close();

                    // print result
                    responseStr = response.toString();
                    System.out.println("JSON Response: " + responseStr);

                    return new JSONArray(arr);
                }
            }
            catch (Exception e){
                System.err.println(responseStr);
                e.printStackTrace();
            }

            return null;
        }
        protected void onPostExecute(JSONArray result)
        {
            if(result == null) return;
            try{
                LatLng cent = null;
                JSONArray arr = result;
                for(int i = 0; i < arr.length(); i++) {
                    JSONObject jo = arr.getJSONObject(i);
                    String tName = jo.getString("trailName");
                    int tId = jo.getInt("trailId");

                    PolylineOptions po = new PolylineOptions().color(Color.rgb(0, 32, 123))
                            .width(5);
                    System.out.println("Trail: " + tName);


                    for(int j = 0; j < jo.getJSONArray("points").length(); j++) {
                        JSONObject jojo = jo.getJSONArray("points").getJSONObject(j);
                        //System.out.println(jojo.toString());
                        po.add(new LatLng(jojo.getDouble("lat"), jojo.getDouble("lng")));
                        if(i == 0 && j == 0) {
                            cent = new LatLng(jojo.getDouble("lat"), jojo.getDouble("lng"));
                        }
                    }

                    Polyline tmp = mMap.addPolyline(po);
                    tmp.setClickable(true);
                    hashMap.put(tmp, tName);
                    nameToId.put(tName,tId);

                }
                System.err.println(hashMap.toString());
                if(cent != null) {
                    CameraUpdate center = CameraUpdateFactory.newLatLng(cent);
                    CameraUpdate zoom = CameraUpdateFactory.zoomTo(10);
                    mMap.moveCamera(center);
                    mMap.moveCamera(zoom);
                }
            }
            catch (Exception e){
                e.printStackTrace();
            }
        }
    }

    private void polyclick(final Polyline polyline)
    {

        //System.out.println(hashMap.get(polyline));
        AlertDialog.Builder alert = new AlertDialog.Builder(this);
        alert.setTitle("Trail");
        alert.setMessage("Trail: " + hashMap.get(polyline));
        alert.setPositiveButton("Go To", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                goToTrailView(nameToId.get(hashMap.get(polyline)));
            }
        });
        alert.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();
            }
        });
        alert.show();
    }

    public void goToTrailView(int trailId)
    {
        Intent intent = new Intent(this, TrailViewActivity.class);
        intent.putExtra("trailId", trailId);
        startActivity(intent);
    }

    public void goToList(View v)
    {
        Intent intent = new Intent(this, ListViewActivity.class);
        startActivity(intent);
        finish();
    }

    public void goToSearch(View v)
    {
        Intent intent = new Intent(this, SearchActivity.class);
        startActivity(intent);
        finish();
    }
}
