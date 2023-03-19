# AVeriTeC Backend
The backend of the AVeriTec is based on PHP. 

There are 5 phases of annotations:

P1: claim nomarlization

P2: question answering

P3: verdict validation

P4: dispute resolution

P5: post control

There are two modes: training and real annotation.
Training only covers P1, P2 and P3.


# Structure

## Create table for real annotation

create_database.php: table for creating the SQL database to store the annotations;
registration_create.php: table for creating the registrated users;
create_cache.php: table for caching the URLs for evidence;
create_record.php: table for storing search records in P2;
create_claims.php: table for storing all the claims;
create_annotators.php: table for storing details for annotators;
create_norm.php: table for annotated claims in P1;
create_qa.php: table for question answer pairs for P2 and P4;
create_assigned_claims.php: table for storing claims assigned to P1;
create_assigned_valids.php:table for storing claims assigned to P3;
create_assigned_norms.php: table for storing claims assigned to P2;
create_assigned_dispute.php: table for storing claims assigned to P4;
create_assigned_post.php: table for storing claims assigned to P5;

## Create table for training
train_create_claims.php: table for storing all the training claims;
train_create_map.php: table for storing the map between user and their annotated claims in P1;
train_create_norms.php: able for storing annotated claims in P1;
train_create_qa.php: table for storing question answer pairs in P2;
train_create_qamap.php: table for storing the map to store user and their annotated claims in P2;
train_create_qaproblem.php: table for storing problems of question answer pairs in P3;
train_create_vvmap.php:  table for storing the map to store user and their annotated claims in P3;

## Get the data from the database (training and real)
get_annotators.php
get_assigned_claims.php
get_assigned_disputes.php
get_assigned_norms.php
get_assigned_post.php
get_assigned_valids.php
get_cache.php
get_claims.php
get_norms.php
get_qas.php
get_record.php
train_get_claims.php
train_get_map.php
train_get_norms.php
train_get_qamap.php
train_get_qaproblem.php
train_get_qas.php
train_get_vvmap.php


## Files for performing functions
admin_control.php: add, modify and deleted the users;
change_password.php: change the password for the user;
registration.php: registrate a new user;
login.php: function for logging in;
web_search.php: performing the web search for P2 and P4;
finished_loading.php: send a message to the front end when finishing loading the webpages.
insert_claims.php: insert all the claims from Google fact-check to the table;
assign_claims.php: assign claims to P1, P2, P3, P4 and P5;
global_statistics.php: get the stats for all users;
user_statistics.php: get the stats for certain user;
claim_norm.php: main function for P1 (training and real)
question_answering.php: main function for P2 (training and real)
verdict_validate.php: main function for P3 (training and real)
dispute_resolution.php: main function for P3 (training and real)
post_control.php: main function for P3 (training and real)

## Table editing
alter_table.php: add a column to an existing table;
update_annotators.php: update the table annotators;
update_claims.php
update_map.php
update_norms.php
update_qapair.php
update_table.php
