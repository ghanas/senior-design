package com.example.anthony.mapprototype;

import android.graphics.Color;
import android.os.AsyncTask;
import android.support.v4.app.FragmentActivity;
import android.os.Bundle;

import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.PolylineOptions;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Arrays;

public class MapsActivity extends FragmentActivity {

    public class CallApi extends AsyncTask<String, Void, JSONArray> {
        protected JSONArray doInBackground(String... urls) {
            try {
                final String USER_AGENT = "Mozilla/5.0";
                final String POST_URL = "https://web.eecs.utk.edu/~ghanas/service.php";
                String POST_PARAMS = "request=getTrailPoints&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";

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

                    // print result
                    JSONArray arr = new JSONArray(response.toString());

                    return arr;
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
        protected void onPostExecute(JSONArray result)
        {
            if(result == null) return;
            try{
                LatLng cent = null;
                JSONArray arr = result;
                for(int i = 0; i < arr.length(); i++) {
                    JSONObject jo = arr.getJSONObject(i);

                    PolylineOptions po = new PolylineOptions();

                    for(int j = 0; j < jo.getJSONArray("points").length(); j++) {
                        JSONObject jojo = jo.getJSONArray("points").getJSONObject(j);
                        System.out.println(jojo.toString());
                        po.add(new LatLng(jojo.getDouble("lat"), jojo.getDouble("lng")));
                        if(i == 0 && j == 0) {
                            cent = new LatLng(jojo.getDouble("lat"), jojo.getDouble("lng"));
                        }
                    }

                    mMap.addPolyline(po);
                }
                if(cent != null) {
                    CameraUpdate center = CameraUpdateFactory.newLatLng(cent);
                    CameraUpdate zoom = CameraUpdateFactory.zoomTo(10);
                    mMap.moveCamera(center);
                    mMap.moveCamera(zoom);
                }
            }
            catch (Exception e){
                System.err.println(e.toString());
                System.err.println(e.getMessage());
                for (int i = 0; i < e.getStackTrace().length; i++)
                    System.err.println(e.getStackTrace()[i].toString());
            }
        }
    }

    public GoogleMap mMap; // Might be null if Google Play services APK is not available.

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maps);
        setUpMapIfNeeded();
    }

    @Override
    protected void onResume() {
        super.onResume();
        setUpMapIfNeeded();
    }

    /**
     * Sets up the map if it is possible to do so (i.e., the Google Play services APK is correctly
     * installed) and the map has not already been instantiated.. This will ensure that we only ever
     * call {@link #setUpMap()} once when {@link #mMap} is not null.
     * <p/>
     * If it isn't installed {@link SupportMapFragment} (and
     * {@link com.google.android.gms.maps.MapView MapView}) will show a prompt for the user to
     * install/update the Google Play services APK on their device.
     * <p/>
     * A user can return to this FragmentActivity after following the prompt and correctly
     * installing/updating/enabling the Google Play services. Since the FragmentActivity may not
     * have been completely destroyed during this process (it is likely that it would only be
     * stopped or paused), {@link #onCreate(Bundle)} may not be called again so we should call this
     * method in {@link #onResume()} to guarantee that it will be called.
     */
    private void setUpMapIfNeeded() {
        // Do a null check to confirm that we have not already instantiated the map.
        if (mMap == null) {
            // Try to obtain the map from the SupportMapFragment.
            mMap = ((SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.map))
                    .getMap();
            // Check if we were successful in obtaining the map.
            if (mMap != null) {
                setUpMap();
            }
        }
    }

    /**
     * This is where we can add markers or lines, add listeners or move the camera. In this case, we
     * just add a marker near Africa.
     * <p/>
     * This should only be called once and when we are sure that {@link #mMap} is not null.
     */
    private void setUpMap() {
        //mMap.addMarker(new MarkerOptions().position(new LatLng(0, 0)).title("Marker"));

        /*
        double knoxLat = 35.9728;
        double knoxLng = -83.9422;

        LatLng points[] = {
                new LatLng(0.349065595 + knoxLat, 0.316211876 + knoxLng),
                new LatLng(0.34297018 + knoxLat, 0.324839475 + knoxLng),
                new LatLng(0.196679908 + knoxLat, 0.445024079 + knoxLng),
                new LatLng(0.184876076 + knoxLat, 0.447632425 + knoxLng),
                new LatLng(0.172394971 + knoxLat, 0.444622793 + knoxLng),
                new LatLng(0.129823725 + knoxLat, 0.393057786 + knoxLng),
                new LatLng(0.040133861 + knoxLat, 0.283607546 + knoxLng),
                new LatLng(0.029781299 + knoxLat, 0.295345111 + knoxLng),
                new LatLng(0.008302189 + knoxLat, 0.391954256 + knoxLng),
                new LatLng(-0.014047718 + knoxLat, 0.486958267 + knoxLng),
                new LatLng(-0.034656056 + knoxLat, 0.5 + knoxLng),
                new LatLng(-0.133440699 + knoxLat, 0.478330658 + knoxLng),
                new LatLng(-0.2301935 + knoxLat, 0.454454254 + knoxLng),
                new LatLng(-0.235901917 + knoxLat, 0.447732746 + knoxLng),
                new LatLng(-0.217615633 + knoxLat, 0.334771281 + knoxLng),
                new LatLng(-0.19700727 + knoxLat, 0.236556994 + knoxLng),
                new LatLng(-0.200103371 + knoxLat, 0.228230349 + knoxLng),
                new LatLng(-0.207359832 + knoxLat, 0.225621997 + knoxLng),
                new LatLng(-0.296469173 + knoxLat, 0.268659697 + knoxLng),
                new LatLng(-0.388190845 + knoxLat, 0.3114968 + knoxLng),
                new LatLng(-0.400962218 + knoxLat, 0.30878813 + knoxLng),
                new LatLng(-0.409766724 + knoxLat, 0.302166926 + knoxLng),
                new LatLng(-0.454273018 + knoxLat, 0.20826647 + knoxLng),
                new LatLng(-0.5 + knoxLat, 0.113764056 + knoxLng),
                new LatLng(-0.48668521 + knoxLat, 0.097211071 + knoxLng),
                new LatLng(-0.397575868 + knoxLat, 0.051364371 + knoxLng),
                new LatLng(-0.309821075 + knoxLat, 0.00611959 + knoxLng),
                new LatLng(-0.306628211 + knoxLat, -0.001103521 + knoxLng),
                new LatLng(-0.309821075 + knoxLat, -0.008527281 + knoxLng),
                new LatLng(-0.397188858 + knoxLat, -0.053872379 + knoxLng),
                new LatLng(-0.48600794 + knoxLat, -0.099317821 + knoxLng),
                new LatLng(-0.5 + knoxLat, -0.116272052 + knoxLng),
                new LatLng(-0.453982759 + knoxLat, -0.211577033 + knoxLng),
                new LatLng(-0.408218678 + knoxLat, -0.306280096 + knoxLng),
                new LatLng(-0.389351879 + knoxLat, -0.31380416 + knoxLng),
                new LatLng(-0.381611654 + knoxLat, -0.31380416 + knoxLng),
                new LatLng(-0.296469173 + knoxLat, -0.271268049 + knoxLng),
                new LatLng(-0.207359832 + knoxLat, -0.228230323 + knoxLng),
                new LatLng(-0.200103371 + knoxLat, -0.230838676 + knoxLng),
                new LatLng(-0.19700727 + knoxLat, -0.239165321 + knoxLng),
                new LatLng(-0.217615633 + knoxLat, -0.337379608 + knoxLng),
                new LatLng(-0.235901917 + knoxLat, -0.450341083 + knoxLng),
                new LatLng(-0.2301935 + knoxLat, -0.457062579 + knoxLng),
                new LatLng(-0.133440699 + knoxLat, -0.48093898 + knoxLng),
                new LatLng(-0.024980791 + knoxLat, -0.5 + knoxLng),
                new LatLng(-0.013950944 + knoxLat, -0.489365956 + knoxLng),
                new LatLng(0.008302189 + knoxLat, -0.394562602 + knoxLng),
                new LatLng(0.029781299 + knoxLat, -0.297953438 + knoxLng),
                new LatLng(0.040133861 + knoxLat, -0.286215873 + knoxLng),
                new LatLng(0.127404883 + knoxLat, -0.392556156 + knoxLng),
                new LatLng(0.172298209 + knoxLat, -0.447231129 + knoxLng),
                new LatLng(0.184876076 + knoxLat, -0.450240765 + knoxLng),
                new LatLng(0.196679908 + knoxLat, -0.447632413 + knoxLng),
                new LatLng(0.343260429 + knoxLat, -0.327046543 + knoxLng),
                new LatLng(0.3510974 + knoxLat, -0.305878799 + knoxLng),
                new LatLng(0.349065595 + knoxLat, -0.295244768 + knoxLng),
                new LatLng(0.266728955 + knoxLat, -0.188302552 + knoxLng),
                new LatLng(0.243508277 + knoxLat, -0.174357944 + knoxLng),
                new LatLng(0.236251815 + knoxLat, -0.17184991 + knoxLng),
                new LatLng(0.169782643 + knoxLat, -0.17184991 + knoxLng),
                new LatLng(0.103216696 + knoxLat, -0.17184991 + knoxLng),
                new LatLng(0.027072233 + knoxLat, -0.189907688 + knoxLng),
                new LatLng(-0.056715701 + knoxLat, -0.207965478 + knoxLng),
                new LatLng(-0.084483764 + knoxLat, -0.201243969 + knoxLng),
                new LatLng(-0.143212714 + knoxLat, -0.154293734 + knoxLng),
                new LatLng(-0.208037092 + knoxLat, -0.091994367 + knoxLng),
                new LatLng(-0.213745509 + knoxLat, -0.00130417 + knoxLng),
                new LatLng(-0.207456582 + knoxLat, 0.090489574 + knoxLng),
                new LatLng(-0.199522861 + knoxLat, 0.103330661 + knoxLng),
                new LatLng(-0.084677263 + knoxLat, 0.198535325 + knoxLng),
                new LatLng(-0.071712385 + knoxLat, 0.20345105 + knoxLng),
                new LatLng(0.027168983 + knoxLat, 0.187299374 + knoxLng),
                new LatLng(0.10341022 + knoxLat, 0.169241584 + knoxLng),
                new LatLng(0.16987938 + knoxLat, 0.169241584 + knoxLng),
                new LatLng(0.236251815 + knoxLat, 0.169241584 + knoxLng),
                new LatLng(0.243701776 + knoxLat, 0.17184991 + knoxLng),
                new LatLng(0.267793239 + knoxLat, 0.186496793 + knoxLng),
                new LatLng(0.349065595 + knoxLat, 0.292636442 + knoxLng),
                new LatLng(0.3510974 + knoxLat, 0.303671756 + knoxLng),
                new LatLng(0.349065595 + knoxLat, 0.316211876 + knoxLng)
        };
        mMap.addPolyline((new PolylineOptions()).addAll(Arrays.asList(points)));

        LatLng points2[] = {
                new LatLng(0.499536551 + knoxLat, 0.101424559 + knoxLng),
                new LatLng(0.494218619 + knoxLat, 0.113712974 + knoxLng),
                new LatLng(0.481423455 + knoxLat, 0.118579463 + knoxLng),
                new LatLng(0.197453955 + knoxLat, 0.118880417 + knoxLng),
                new LatLng(-0.095283537 + knoxLat, 0.118467795 + knoxLng),
                new LatLng(-0.105546896 + knoxLat, 0.111806368 + knoxLng),
                new LatLng(-0.108768713 + knoxLat, 0.098515253 + knoxLng),
                new LatLng(-0.108768713 + knoxLat, -0.00130417 + knoxLng),
                new LatLng(-0.108430955 + knoxLat, -0.105431381 + knoxLng),
                new LatLng(-0.104249823 + knoxLat, -0.115871956 + knoxLng),
                new LatLng(-0.093732092 + knoxLat, -0.121689404 + knoxLng),
                new LatLng(0.195035114 + knoxLat, -0.121689404 + knoxLng),
                new LatLng(0.481959756 + knoxLat, -0.121416794 + knoxLng),
                new LatLng(0.494860487 + knoxLat, -0.116986582 + knoxLng),
                new LatLng(0.499806488 + knoxLat, -0.103330648 + knoxLng),
                new LatLng(0.5 + knoxLat, -0.002909305 + knoxLng),
                new LatLng(0.499536551 + knoxLat, 0.101424559 + knoxLng)
        };
        mMap.addPolyline((new PolylineOptions()).addAll(Arrays.asList(points2)));
        */

        new CallApi().execute();
    }
}
