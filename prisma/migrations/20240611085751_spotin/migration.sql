/*
  Warnings:

  - A unique constraint covering the columns `[email]` on the table `users` will be added. If there are existing duplicate values, this will fail.
  - A unique constraint covering the columns `[reset_password_request_id]` on the table `users` will be added. If there are existing duplicate values, this will fail.

*/
-- AlterTable
ALTER TABLE "users" ADD COLUMN     "reset_password_request_id" TEXT,
ALTER COLUMN "enabled" SET DEFAULT true;

-- CreateTable
CREATE TABLE "reset_password_requests" (
    "id" TEXT NOT NULL,
    "token" VARCHAR(255) NOT NULL,
    "created_at" TIMESTAMPTZ(6) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMPTZ(6) NOT NULL,

    CONSTRAINT "reset_password_requests_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE UNIQUE INDEX "reset_password_requests_token_key" ON "reset_password_requests"("token");

-- CreateIndex
CREATE UNIQUE INDEX "users_email_key" ON "users"("email");

-- CreateIndex
CREATE UNIQUE INDEX "users_reset_password_request_id_key" ON "users"("reset_password_request_id");

-- AddForeignKey
ALTER TABLE "users" ADD CONSTRAINT "users_reset_password_request_id_fkey" FOREIGN KEY ("reset_password_request_id") REFERENCES "reset_password_requests"("id") ON DELETE SET NULL ON UPDATE CASCADE;
