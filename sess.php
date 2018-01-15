 <?php
 ini_set('session.gc_maxlifetime', 9999);
printf("cookie: %s, gc: %s", ini_get('session.cookie_lifetime'), ini_get('session.gc_maxlifetime')); 