package com.taoh.draft.artofhiking;

import android.util.SparseBooleanArray;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by ctester3 on 3/10/16.
 */
public class FilterData {
    private static FilterData _instace;
    public HashMap<String, Integer> regionHM;
    public HashMap<String, Integer> interestPointsHM;
    public SparseBooleanArray sRegions;
    public SparseBooleanArray sIPs;
    public ArrayList<Integer> selectedRegions;
    public ArrayList<Integer> selectedIPs;
    public Double minElevationGain;
    public Double maxElevationGain;
    public Double minTrailLength;
    public Double maxTrailLength;
    public Double selMinElevationGain;
    public Double selMaxElevationGain;
    public Double selMinTrailLength;
    public Double selMaxTrailLength;
    public String horseAccessibility;
    private boolean set = false;

    public static FilterData getInstance() {
        if(_instace == null) {
            _instace = new FilterData();
        }
        return _instace;
    }

    private FilterData() {
        regionHM = null;
        interestPointsHM = null;
        minElevationGain = null;
        maxElevationGain = null;
        minTrailLength = null;
        maxTrailLength = null;
        selMaxTrailLength = null;
        selMinTrailLength = null;
        selMaxElevationGain = null;
        selMinElevationGain = null;
        selectedIPs = null;
        selectedRegions = null;
        sIPs = null;
        sRegions = null;
        set = false;
        horseAccessibility = "Default";
    }
    public boolean isSet()
    {
        return set;
    }
    public void setValues(double minTL, double minEG, double maxTL, double maxEG,double cminTL,double cminEG,double cmaxTL,double cmaxEG, HashMap<String, Integer> hmRegion, HashMap<String, Integer> hmIP, ArrayList<Integer> selRegions, ArrayList<Integer> selIPs, SparseBooleanArray selBoolsRegions, SparseBooleanArray selBoolIPs, String hrsAcc)
    {
        regionHM = hmRegion;
        interestPointsHM = hmIP;
        minElevationGain = minEG;
        maxElevationGain = maxEG;
        minTrailLength = minTL;
        maxTrailLength = maxTL;
        selMaxTrailLength = cmaxTL;
        selMinTrailLength = cminTL;
        selMaxElevationGain = cmaxEG;
        selMinElevationGain = cminEG;
        selectedRegions = selRegions;
        selectedIPs = selIPs;
        sRegions = selBoolsRegions;
        sIPs = selBoolIPs;
        horseAccessibility = hrsAcc;
        set = true;
    }
    public String getQueryString()
    {
        String ret = "";
        if(set) {
            String rTex = (selectedRegions.isEmpty())? "default" :android.text.TextUtils.join(",", selectedRegions);
            String iTex = (selectedIPs.isEmpty())? "default" :android.text.TextUtils.join(",", selectedIPs);
            ret = ret + "regions=" + rTex;
            ret = ret + "&minLen=" + selMinTrailLength;
            ret = ret + "&maxLen=" + selMaxTrailLength;
            ret = ret + "&minElevationGain=" + selMinElevationGain;
            ret = ret + "&maxElevationGain=" + selMaxElevationGain;
            ret = ret + "&horse=" + horseAccessibility;
            ret = ret + "&intPoints=" + iTex;
        } else {
            ret = ret + "regions=default";
            ret = ret + "&minLen=" + minTrailLength;
            ret = ret + "&maxLen=" + maxTrailLength;
            ret = ret + "&minElevationGain=" + minElevationGain;
            ret = ret + "&maxElevationGain=" + maxElevationGain;
            ret = ret + "&horse=" + horseAccessibility;
            ret = ret + "&intPoints=default";
        }
        return ret;

    }
}
