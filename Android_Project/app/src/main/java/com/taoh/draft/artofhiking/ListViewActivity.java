package com.taoh.draft.artofhiking;

import android.content.Intent;
import android.graphics.drawable.BitmapDrawable;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;

public class ListViewActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_view);
    }

    @Override
    protected void onDestroy() {
        System.out.println("ListView Destroyed");
        super.onDestroy();
        unbindDrawables(findViewById(R.id.lvLayout));
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
    public void goToMain(View v)
    {
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);
        finish();
    }
}