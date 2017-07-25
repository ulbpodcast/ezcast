# How to install EZcast ? 

## Requirements

In order to use EZcast, you need to install / enable several components:

- Apache2
- MySQL 5.x 
- PHP5.3 ( or greater ) with following extensions activated:
  -curl
  -[APC (for performance and upload progress bar PHP 5.3->5.4 )]
  -OPCache (for performance on PHP 5.5 )
  -ldap
  -pdo_mysql 
- ssh (unix command, usually installed by default)
- at (unix command, usually installed by default)
- rsync (unix command, usually installed by default)

## Quick Installation

1. Download the latest available version of EZcast from our Git repository https://github.com/ulbpodcast/ezcast.git
2. Create a MySQL database for EZcast on your server (grant all privileges on the database).
3. Put the EZcast directory wherever you want on your server (we recommend to place it under « /usr/local/ezcast»)
4. Edit the php.ini file to increase upload_max_filesize, post_max_size, max_execution_time and max_input_time.
5. Launch the « install.sh » script as root from the EZcast directory and follow the instructions.
6. Open the EZadmin page in your web browser for the configuration of EZcast and all of its components. This is a one-time operation
`i.e :   http://my.server.address/ezadmin`
7. Create the first renderer for EZcast in EZadmin web interface
#

## Detailed installation 

### 1. Download the source code

Open your terminal and call Git in command line to download the source code of EZcast
```
root# cd <wherever you want to install ezcast>
root# git clone https://github.com/ulbpodcast/ezcast.git
```

### 2. Create a MySQL database

Open your terminal and call mysql in command line to create a user and the database for EZadmin. 
The database will be used to store users and courses for the EZcast components. 
Make sure you have sufficient permissions to create a user and a database in MySQL. If you don't, call your database administrator.

Please refer to the following example to configure your database. You can choose the user and database names as you fit.

```
$ mysql -u root -p
Enter password :
Welcome to the MySQL monitor.  Commands end with ; or \g.
Your MySQL connection id is 5340 to server version: 3.23.54

Type 'help;' or '\h' for help. Type '\c' to clear the buffer.

Mysql> CREATE USER <ezcast_user_name> IDENTIFIED BY '<ezcast_password>';

mysql> CREATE DATABASE <database_name> CHARACTER SET utf8 COLLATE utf8_general_ci;
Query OK, 1 row affected (0.00 sec)

mysql> GRANT ALL PRIVILEGES ON <database_name>.* TO '<ezcast_user_name>'@'<host>' IDENTIFIED BY '<ezcast_password>';
Query OK, 0 rows affected (0.00 sec)
 
mysql> FLUSH PRIVILEGES;
Query OK, 0 rows affected (0.01 sec)

Mysql> exit
Bye
$
```
Examples of values:
  * ezcast_user_name: ezcast or ezadmin
  * database_name: ezcast

### 3. Move the EZcast directory

Now you have to decide where you want to install EZcast and its components. We recommend you to install it under « /usr/local/ » directory. 

```
cd
#change user to be root (alternatively, use 'sudo bash' depending on the distribution)
su
#following line creates the directories /usr/local/ if they don’t exist yet
mkdir –p /usr/local/
#moves the ezcast directory from current user’s home dir to /usr/local
#change the following path if you want to install ezcast somewhere else
mv ezcast /usr/local
```

### 4. Configure PHP

Since EZmanager allows you to submit your own video files, you may want to increase the max file size for upload.

```
# this depends on your distribution 
vi /etc/php5/apache2/php.ini
# change upload_max_filesize in the ‘File uploads’ section
upload_max_filesize = 2000M 
post_max_size = 2000M
max_execution_time = 300
max_input_time = 300
```

### 5. Installing EZcast components

Go in the ezcast folder. Make sure the file ‘install.sh’ can be executed. 
Start ‘install.sh’ as root and follow the instructions written on the screen.

### 6. Configuring EZcast

Go to the EZadmin webpage at `http://your.server.address/ezadmin`. At the first visit, you will be redirected to a configuration page before you can run EZcast.

You can also configure EZcast by editing the file config manually. To do this, go to the EZcast folder. For each subdirectories (ezmanager / ezadmin / ezplayer / common), copy ‘config-sample.inc’ to ‘config.inc’ and edit it.

### 7. Creating your first renderer

Create the first renderer for EZcast in EZadmin web interface. 
Prepare your renderer by installing all dependencies. Refer to EZrenderer's documentation to know what are the required dependencies.

Click on "Create renderer" in the left menu of EZadmin web interface and follow instructions on the screen. 


EZcast is now installed. Before using it, make sure EZrenderer is installed. It is required to process the submitted movies. 
You can also install EZrecorder which is the interface used to record in the classroom.

Once EZrenderer has been installed, you can use the different interfaces using the following URL’s.

```
http://your.server.address/ezadmin
http://your.server.address/ezmanager
http://your.server.address/ezplayer
``` 
