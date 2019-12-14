INSERT INTO larv_db.tasks 
(uri_access,label,html_attr,icon,`group`,`position`,is_visible,is_protected,quick_access,user_type,menu_order,description)
SELECT 
task_access,task_name,task_attr,task_icon,task_group,task_position,task_visible,is_logged_user,quick_access,user_type,task_order,task_description FROM sister_1802.uac_tasks;


INSERT INTO larv_db.users
(user_id,username,password,fullname,email,phone,avatar_url,is_disabled,user_type,user_code_number,group_name,settings)
SELECT 
user_id,user_name,user_pass,user_fullname,user_email,user_phone,user_avatar,user_status,user_type,user_code_number,group_name,user_setting
FROM sister_1802.uac_users


INSERT INTO larv_db.roles
(name,label,description)
SELECT 
role_name,role_label,role_description
FROM sister_1802.uac_roles


INSERT INTO larv_db.groups
(name,label,description)
SELECT 
group_name,group_label,group_description
FROM sister_1802.uac_groups


INSERT INTO larv_db.role_actors
(role_name,user_id,group_name)
SELECT 
role_name,user_id,group_name
FROM sister_1802.uac_role_actors


INSERT INTO larv_db.permissions
(uri_access,user_id,group_name,role_name)
SELECT 
task_access,user_id,group_name,role_name
FROM sister_1802.uac_permissions


INSERT INTO larv_db.access_data
(user_id,access_name,access_type)
SELECT 
user_id,access_name,access_type
FROM sister_1802.uac_access_data
