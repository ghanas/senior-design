import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class SampleReciever {
    
    private static final String USER_AGENT = "Mozilla/5.0";
    
    private static final String POST_URL = "https://web.eecs.utk.edu/~ghanas/service.php";
    
    private static String POST_PARAMS = "request=InvalidFunction";
    
    public static void main(String[] args) throws IOException {
        System.out.println("SENDING POST");
        send(1);
        System.out.println("POST DONE\n");
    }
    
    private static void send(int task) throws IOException {
        URL obj = new URL(POST_URL);
        HttpURLConnection con = (HttpURLConnection) obj.openConnection();
        con.setRequestMethod("POST");
        con.setRequestProperty("User-Agent", USER_AGENT);
        
        con.setDoOutput(true);
        OutputStream os = con.getOutputStream();
        
        switch (task) {
            case 1:
                //POST_PARAMS = "request=getTrailPoints&regions=1&minLen=0.7&maxLen=6.0&minElevationGain=444.60&maxElevationGain=1044.80&horse=default&intPoints=default&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
                //POST_PARAMS = "request=getFilters&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
                POST_PARAMS = "request=getTrailInfo&trailId=1&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
//								POST_PARAMS = "regions=1&minLen=0.6&maxLen=10.176&minElevationGain=27.78&maxElevationGain=1080.82&horse=false&intPoints=13,7,6&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getTrailPoints&regions=5,1&minLen=0.6&maxLen=17.0&minElevationGain=27.78&maxElevationGain=1080.82&horse=default&intPoints=default&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getTrailInfo&trailId=1&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getTrailPoints&regions=default&minLen=0.6&maxLen=17.0&minElevationGain=27.78&maxElevationGain=1080.82&horse=default&intPoints=default&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getTrailPointsForTrailId&trailId=1&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getTrailBuilderInfo&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getParkingLots&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getTrailPoints&regions=default&minLen=0.6&maxLen=17.0&minElevationGain=27.78&maxElevationGain=1080.82&horse=default&intPoints=default&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								//POST_PARAMS = "request=getListView&regions=1&minLen=0.6&maxLen=17.0&minElevationGain=27.78&maxElevationGain=1080.82&horse=default&intPoints=11&tokenId=9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92";
								break;
            default:
                break;
        }
        os.write(POST_PARAMS.getBytes());
        
        
        os.flush();
        os.close();
        
        int responseCode = con.getResponseCode();
        System.out.println("POST Response Code :: " + responseCode);
        
        if (responseCode == HttpURLConnection.HTTP_OK) { //success
            BufferedReader in = new BufferedReader(new InputStreamReader(
                                                                         con.getInputStream()));
            String inputLine;
            StringBuffer response = new StringBuffer();
            
            while ((inputLine = in.readLine()) != null) {
                response.append(inputLine);
            }
            in.close();
            
            // print result
            System.out.println(response.toString());
        } else {
            System.out.println("POST request borked");
        }
    }
    
}
