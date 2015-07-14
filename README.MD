
Make sure to chmod 'tmp' folder to 777

Hosting the database on the same system as the panel will cut load times by quite a bit.
Comparison:
    Between 0.1s for remote API calls to 0.02s per local API call.

Notes:
    All data handled is going to be in the form of JSON, as it is cleaner, easier to manage, and faster to decode/encode than serializing/xml.
    XML could be supported soon, however most people may prefer JSON over XML.


TODO:
    + Set-up/finish installation page (set-up json file)
    - Set-up/finish variables for templates servers (May need some changes in API and panel code to accept variables) // Not fully important
   | - Begin work on server specific pages (edit, view details, delete) // Partially.
   | - Add similar work as above for node specific pages. (delete sends file from panel)
   -- Set-up checking of perms for each script (updateSettings) to make sure user has access.

   + Check Node install script on linux - Get a way to fetch the node and use it in status insert's.

