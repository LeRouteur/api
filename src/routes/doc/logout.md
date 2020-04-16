**Logout**
----
Calling this service permits to log out the user.

* **URL**

  https://secureconnect.online/api/auth/logout

* **Method:**

  `GET`

* **Success Response:**

  * **Code:** 200 OK<br/>
    **Content:** `{"success":"User Logged Out"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br/>
    **Content:** `{"error":"User Not Logged"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/auth/logout",
      dataType: "json",
      type : "GET",
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

    Logging out destroys the session.