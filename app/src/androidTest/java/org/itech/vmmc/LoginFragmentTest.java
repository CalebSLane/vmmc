package org.itech.vmmc;

import android.support.test.runner.AndroidJUnit4;
import org.junit.Test;
import org.junit.runner.RunWith;

public class LoginFragmentTest {
    LoginFragment login;

    @Test
    public void checkCredentialsTrue() throws Exception {
        Assert.assertTrue(login.checkLoginCredentials("sync@", "password"));
    }

    @Before
    public void setUp() throws Exception {
        login = new LoginFragment();
    }


}