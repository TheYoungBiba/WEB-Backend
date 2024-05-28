CREATE TABLE verified_users (
    id int auto_increment primary key,
    login varchar(255) not null,
    password varchar(255) not null ,
    role varchar(16) default 'user',
    unique (login)
);