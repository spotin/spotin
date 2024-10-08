/*
  Warnings:

  - You are about to drop the column `reset_password_request_id` on the `users` table. All the data in the column will be lost.
  - A unique constraint covering the columns `[user_id]` on the table `reset_password_requests` will be added. If there are existing duplicate values, this will fail.
  - Added the required column `user_id` to the `reset_password_requests` table without a default value. This is not possible if the table is not empty.

*/
-- DropForeignKey
ALTER TABLE "spots" DROP CONSTRAINT "spots_user_id_fkey";

-- DropForeignKey
ALTER TABLE "tokens" DROP CONSTRAINT "tokens_user_id_fkey";

-- DropForeignKey
ALTER TABLE "users" DROP CONSTRAINT "users_reset_password_request_id_fkey";

/** So in order to not loose the data in the reset_password_request_id column,
we need to first create a new column in the reset_password_requests table, then
copy the data from the users table to the reset_password_requests table, then
drop the reset_password_request_id column from the users table, then create a
foreign key from the reset_password_requests table to the users table, then
create a foreign key from the spots table to the users table, then create a
foreign key from the tokens table to the users table. **/

ALTER TABLE "reset_password_requests" ADD COLUMN "user_id" TEXT;

UPDATE reset_password_requests
SET user_id = (
  SELECT id
  FROM users
  WHERE reset_password_requests.id = users.reset_password_request_id
);

ALTER TABLE "reset_password_requests" ALTER COLUMN "user_id" SET NOT NULL;

-- DropIndex
DROP INDEX "users_reset_password_request_id_key";

-- AlterTable
ALTER TABLE "users" DROP COLUMN "reset_password_request_id";

-- CreateIndex
CREATE UNIQUE INDEX "reset_password_requests_user_id_key" ON "reset_password_requests"("user_id");

-- AddForeignKey
ALTER TABLE "spots" ADD CONSTRAINT "spots_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE ON UPDATE NO ACTION;

-- AddForeignKey
ALTER TABLE "tokens" ADD CONSTRAINT "tokens_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE ON UPDATE NO ACTION;

-- AddForeignKey
ALTER TABLE "reset_password_requests" ADD CONSTRAINT "reset_password_requests_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE CASCADE ON UPDATE NO ACTION;
