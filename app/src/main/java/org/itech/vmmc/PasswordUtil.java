package org.itech.vmmc;


//import de.mkammerer.argon2.Argon2;
//import de.mkammerer.argon2.Argon2Factory;

import org.mindrot.jbcrypt.BCrypt;

public class PasswordUtil {

    //private Argon2 argon2;
    private BCrypt bcrypt;
    private static int WORK = 12;

    public PasswordUtil() {
        //argon2 = Argon2Factory.create();
    }

    public String secureHashPassword(String password) {
        //String hash = argon2.hash(2, 65536, 1, password);
        String hash = BCrypt.hashpw(password, BCrypt.gensalt(WORK));
        return hash;
    }

    public boolean verifyPassword(String password, String hash) {
        //boolean result = argon2.verify(hash, password);
        boolean result = BCrypt.checkpw(password, hash);
        return result;
    }
}
