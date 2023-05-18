/*
  Warnings:

  - Made the column `name` on table `tokens` required. This step will fail if there are existing NULL values in that column.

*/
-- AlterTable
ALTER TABLE "tokens" ALTER COLUMN "name" SET NOT NULL;
