# Mailstack

Postfix and Dovecot mailstack with tine20 db layout

### docker images
Docker images are created and pushed manually atm. for example

    docker buildx build \
      --push \
      --platform linux/arm64/v8,linux/amd64 \
      --tag dockerregistry.metaways.net/tine20/docker/mailstackcontrol:1.0.5 \
      .

## Dovecot

### dovecot_users

```
+--- ---+-----------+-----------------+-----------------------+-----------------------+-------------+---------------+-------------------+--------------------+-------+-------+--------------------------------------+------------+-----------------+--------------+
| userid| domain    | username        | loginname             | password              | quota_bytes | quota_message | quota_sieve_bytes | quota_sieve_script | uid   | gid   | home                                 | last_login | last_login_unix | instancename |
+--- ---+-----------+-----------------+-----------------------+-----------------------+-------------+---------------+-------------------+--------------------+-------+-------+--------------------------------------+------------+-----------------+--------------+
| c08e6 | mail.test | c08e6@tine.test | tine20admin@mail.test | {SSHA256}u2CRFawz3... |        2000 |             0 |                 0 |                  0 | vmail | vmail | /var/vmail/mail.test/c08e6@tine.test | NULL       |            NULL | tine.test    |
+--- ---+-----------+-----------------+-----------------------+-----------------------+-------------+---------------+-------------------+--------------------+-------+-------+--------------------------------------+------------+-----------------+--------------+
```
+ userid: db id
+ domain: not used by dovecot
+ username: userid@tineinstancename, mailbox name and is used to foreword mail from postfix to dovecot
+ loginname: users email, not used by dovecot (at least in the docker stack)
+ password: hashed password prefixed with password hashmethod
+ quota_bytes:
+ quota_message:
+ quota_sieve_bytes:
+ quota_sieve_script:
+ uid: unix user dovecot uses to access the mailbox folder, if null vmail (at least in the docker stack)
+ gid: unix group dovecot uses to access the mailbox folder, if null vmail (at least in the docker stack)
+ home: path to mailbox folder, if null /var/vmail/%domain/%username (at least in the docker stack)
+ last_login: not used by dovecot
+ last_login_unix: not used by dovecot
+ instancename: tine instancename, not used by dovecot


### dovecot_usage
```
+-----------------+---------+----------+
| username        | storage | messages |
+-----------------+---------+----------+
| c08e6@tine.test |     974 |        1 |
+-----------------+---------+----------+
```
gets filled by dovecot

## Postfix

### smtp_users
```
+-------+-------------+-----------------+---------------------------+-----------------------+--------------+
| userid| client_idnr | username        | passwd                    | email                 | forward_only |
+-------+-------------+-----------------+---------------------------+-----------------------+--------------+
| c08e6 | e0fa22      | c08e6@tine.test | {SSHA256}ZHNRRlz7ap2kO... | tine20admin@mail.test |            0 |
+-------+-------------+-----------------+---------------------------+-----------------------+--------------+
```

+ userid: db id
+ client_idnr: not used by postfix
+ username: userid@tineinstancename (dovecotusername), if not set mailbox mapping fails (seems redundant, to alias maps)
+ passwd: for auth not used (at least in the docker stack)
+ email: not used (at least in the docker stack)
+ forward_only: if true incoming emails arent accepted

### smtp_destination
```
+--------+-----------------------+-----------------+
| userid | source                | destination     |
+--------+-----------------------+-----------------+
|  c08e6 | tine20admin@mail.test | c08e6@tine.test |
|  c08e6 |       c08e6@tine.test | c08e6@tine.test |
+--------+-----------------------+-----------------+
```
+ userid: db id
+ source: email or dovecotusername,
+ destination: serid@tineinstancename (dovecotusername)

- maps email addresses to dovecot usernames
- if an email address is not mapped, incoming emails to it are rejected
- postfix domains are all domains occurring in source