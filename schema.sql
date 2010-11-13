BEGIN;
CREATE TABLE "foto" (
    "id" integer NOT NULL PRIMARY KEY,
    "image" varchar(100) NOT NULL,
    "album_id" integer NOT NULL
)
;
CREATE TABLE "category" (
    "id" integer NOT NULL PRIMARY KEY,
    "slug" varchar(50) NOT NULL UNIQUE,
    "name" varchar(60) NOT NULL,
    "parent_id" integer
)
;
CREATE TABLE "album" (
    "id" integer NOT NULL PRIMARY KEY,
    "slug" varchar(50) NOT NULL UNIQUE,
    "name" varchar(60) NOT NULL,
    "description" text NOT NULL,
    "category_id" integer NOT NULL REFERENCES "category" ("id"),
    "created" datetime NOT NULL
)
;
COMMIT;

