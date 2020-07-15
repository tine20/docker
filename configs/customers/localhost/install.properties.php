adminLoginName=tine20admin
adminPassword=tine20admin
adminEmailAddress=tine20admin@mail.test

<?php if ($this->active('ldap') == True) { ?>
authentication="bindRequiresDn:1,backend:ldap,host:ldap\\://ldap,port:389,username:cn=admin\\,dc=tine\\,dc=test,password:admin,baseDn:ou=users\\,dc=tine\\,dc=test,accountFilterFormat:(&(objectClass=posixAccount)(uid=%s)),accountCanonicalForm:2"
accounts="backend:ldap,host:ldap\\://ldap,port:389,username:cn=admin\\,dc=tine\\,dc=test,password:admin,userDn:ou=users\\,dc=tine\\,dc=test,groupsDn:ou=groups\\,dc=tine\\,dc=test,defaultUserGroupName:Users,defaultAdminGroupName:Administrators"
<?php } ?>

<?php if ($this->active('mailstack') == True) { ?>
imap="active:true,host:dovecot,port:143,useSystemAccount:1,ssl:tls,verifyPeer:0,backend:dovecot_imap,domain:mail.test,instanceName:tine.test,dovecot_host:db,dovecot_dbname:dovecot,dovecot_username:tine20,dovecot_password:tine20pw,dovecot_uid:vmail,dovecot_gid:vmail,dovecot_home:/var/vmail/%d/%u,dovecot_scheme:SSHA256"
smtp="active:true,backend:postfix,hostname:postfix,port:25,ssl:none,auth:none,name:postfix,primarydomain:mail.test,secondarydomains:secondary.test,instanceName:tine.test,postfix_host:db,postfix_dbname:postfix,postfix_username:tine20,postfix_password:tine20pw"
sieve="hostname:dovecot,port:4190,ssl:none"
<?php } ?>