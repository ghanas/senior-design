package com.taoh.draft.artofhiking;

import android.graphics.Color;
import android.graphics.DashPathEffect;
import android.graphics.Paint;
import android.graphics.Typeface;
import android.location.Location;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.TextView;

import com.androidplot.ui.AnchorPosition;
import com.androidplot.ui.XLayoutStyle;
import com.androidplot.ui.YLayoutStyle;
import com.androidplot.util.PixelUtils;
import com.androidplot.xy.CatmullRomInterpolator;
import com.androidplot.xy.FillDirection;
import com.androidplot.xy.LineAndPointFormatter;
import com.androidplot.xy.PointLabelFormatter;
import com.androidplot.xy.SimpleXYSeries;
import com.androidplot.xy.XYPlot;
import com.androidplot.xy.XYSeries;
import com.androidplot.xy.XYStepMode;
import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.Polyline;
import com.google.android.gms.maps.model.PolylineOptions;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.Console;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.math.RoundingMode;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.DecimalFormat;
import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

public class TrailViewActivity extends AppCompatActivity implements OnMapReadyCallback {
    private GoogleMap mMap;
    private List<Marker> mMarkers;
    private XYPlot plot;
    private int trailId;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_trail_view);
        trailId = getIntent().getIntExtra("trailId", -1);
        SupportMapFragment mf = ((SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.MapView));
        if(mf != null) {
            mf.getMapAsync(this);
        } else {
            System.err.println("MapFragment null - there was an error...");
        }

        plot = (XYPlot) findViewById(R.id.ElevationView);

        plot.getDomainLabelWidget().position(
                0, XLayoutStyle.ABSOLUTE_FROM_CENTER,
                0, YLayoutStyle.RELATIVE_TO_BOTTOM,
                AnchorPosition.BOTTOM_MIDDLE);


    }

    @Override
    public void onMapReady(GoogleMap map) {
        mMap = map;
        mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(new LatLng(35.972778, -83.942222), 2f));
        mMap.getUiSettings().setAllGesturesEnabled(false);

        map.setOnMarkerClickListener(new GoogleMap.OnMarkerClickListener() {
            @Override
            public boolean onMarkerClick(Marker marker) {
                return true;
            }
        });

        new CallApi().execute();
    }

    public class CallApi extends AsyncTask<String, Void, JSONObject> {
        protected JSONObject doInBackground(String... urls) {
            String responseStr = null;
            try {
                String params = "request=getTrailInfo&trailId=" + trailId;
                final String USER_AGENT = "Mozilla/5.0";
                final String POST_URL = "https://web.eecs.utk.edu/~ghanas/service.php";
                String POST_PARAMS = params + "&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";

                URL urlObj = new URL(POST_URL);
                HttpURLConnection con = (HttpURLConnection) urlObj.openConnection();
                con.setRequestMethod("POST");
                con.setRequestProperty("User-Agent", USER_AGENT);

                con.setDoOutput(true);
                OutputStream os = con.getOutputStream();

                //Write to the request body
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

                    // print result
                    responseStr = response.toString();
                    JSONObject obj = new JSONObject(responseStr);

                    return obj;
                }
            }
            catch (Exception e){
                System.err.println(responseStr);
                e.printStackTrace();
            }

            return null;
        }
        protected void onPostExecute(JSONObject result)
        {
            if(result == null) return;



            try{
                TextView trailName = (TextView) findViewById(R.id.TrailName);
                trailName.setText(result.getString("trailName"));

                int horseAccessibility = result.getInt("horseAccessible");

                TextView description = (TextView) findViewById(R.id.Description);
                StringBuilder descriptionBuilder = new StringBuilder();
                descriptionBuilder.append(result.getString("trailDescription"));
                descriptionBuilder.append("\n");
                if (horseAccessibility == 1)
                    descriptionBuilder.append("This trail is not horse accessible");
                else if (horseAccessibility == 2)
                    descriptionBuilder.append("This trail is horse accessible");
                else if (horseAccessibility == 3)
                    descriptionBuilder.append("This trail is partially horse accessible");
                else
                    descriptionBuilder.append("Invalid value in horse accessibility");
                if (result.getInt("isLoop") == 1)
                    descriptionBuilder.append(" and it is a loop");
                descriptionBuilder.append(".");
                description.setText(descriptionBuilder);

                TextView trailLength = (TextView) findViewById(R.id.TrailLength);
                trailLength.setText(result.getString("trailLength") + " miles");

                TextView trailDifficulty = (TextView) findViewById(R.id.TrailDifficulty);
                trailDifficulty.setText(result.getString("trailDifficulty"));

                TextView elevationGain = (TextView) findViewById(R.id.ElevationGain);
                elevationGain.setText(result.getString("elevationGain") + " meters");

                TextView horseAccessible = (TextView) findViewById(R.id.HorseAccessible);
                horseAccessible.setText(result.getString("horseAccessible"));

                TextView isLoop = (TextView) findViewById(R.id.IsLoop);
                isLoop.setText(result.getString("isLoop"));

                JSONArray trailPoints = result.getJSONArray("trailPoints");
                ArrayList<LatLng> points = new ArrayList<>();

                SimpleXYSeries path = new SimpleXYSeries("Path");

                LatLngBounds.Builder bounds = new LatLngBounds.Builder();

                float total = 0; //meters
                for (int i = 0; i < trailPoints.length(); i++) {
                    JSONObject jo = trailPoints.getJSONObject(i);

                    LatLng point = new LatLng(jo.getDouble("lat"), jo.getDouble("long"));
                    points.add(point);
                    bounds.include(point);

                    if (i == 0) {
                        path.addLast(0, jo.getDouble("elev"));
                    }
                    else {
                        float [] out = new float[5];
                        LatLng prev = points.get(points.size() - 2);
                        LatLng next = points.get(points.size() - 1);
                        Location.distanceBetween(prev.latitude, prev.longitude, next.latitude, next.longitude, out);
                        total += out[0];
                        path.addLast(total * 0.000621371, jo.getDouble("elev"));
                    }
                }

                mMap.addPolyline(new PolylineOptions()
                        .addAll(points)
                        .color(Color.RED)
                        .width(5));

                CameraUpdate cu = CameraUpdateFactory.newLatLngBounds(bounds.build(), 20);
                mMap.moveCamera(cu);

                LineAndPointFormatter pathFormat = new LineAndPointFormatter(
                        Color.argb(255, 173, 206, 255),              //Line Color
                        Color.TRANSPARENT,                           //Point Color
                        Color.argb(128, 173, 206, 255),              //Fill Color
                        new PointLabelFormatter(Color.TRANSPARENT)); //Text Color

                pathFormat.setInterpolationParams(
                        new CatmullRomInterpolator.Params(10, CatmullRomInterpolator.Type.Centripetal));

                // add a new series' to the xyplot:
                plot.addSeries(path, pathFormat);

                // rotate domain labels 45 degrees to make them more compact horizontally:
                plot.getGraphWidget().setDomainLabelOrientation(0);
                plot.setDomainStep(XYStepMode.INCREMENT_BY_VAL, 0.1);
                plot.setRangeStep(XYStepMode.INCREMENT_BY_VAL, 10);
                plot.setTicksPerRangeLabel(2);
                NumberFormat rangeValueFormat = NumberFormat.getInstance();
                rangeValueFormat.setMaximumFractionDigits(0);
                plot.setRangeValueFormat(rangeValueFormat);
                plot.setRangeBottomMax(1450);
                plot.setRangeTopMin(1550);


                plot.redraw();

                /*
                ListView interestPointList = (ListView) findViewById(R.id.InterestPointList);
                JSONArray interestPoints = result.getJSONArray("interestPoints");
                List<Map<String, String>> ipListData = new ArrayList<>();
                mMarkers = new ArrayList<>();
                for (int i = 0; i < interestPoints.length(); i++) {
                    JSONObject jo = interestPoints.getJSONObject(i);
                    String type = jo.getString("type");
                    LatLng point = new LatLng(jo.getDouble("latitudePoint"), jo.getDouble("longitudePoint"));
                    Map<String, String> ipDataMap = new HashMap<>(2);
                    ipDataMap.put("title", type);
                    ipDataMap.put("subtitle", point.toString());
                    ipListData.add(ipDataMap);

                    mMarkers.add(mMap.addMarker(new MarkerOptions()
                            .position(point)
                            .title(type)
                            .icon(BitmapDescriptorFactory.fromResource(R.drawable.crosshair_circled))));
                }

                SimpleAdapter adapter = new SimpleAdapter(TrailViewActivity.this, ipListData,
                        android.R.layout.simple_list_item_2,
                        new String[] {"title", "subtitle"},
                        new int[] {android.R.id.text1,
                                android.R.id.text2});

                interestPointList.setAdapter(adapter);
                */
            }
            catch (Exception e){
                e.printStackTrace();
            }
        }
    }
}
