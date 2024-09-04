-- AlterTable
ALTER TABLE "spots" RENAME COLUMN "redirection" TO "websiteTarget";
ALTER TABLE "spots" RENAME COLUMN "referenced" TO "public";
ALTER TABLE "spots" RENAME COLUMN "title" TO "name";
