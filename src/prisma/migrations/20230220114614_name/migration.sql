-- CreateTable
CREATE TABLE "SequelizeMeta" (
    "name" VARCHAR(255) NOT NULL,

    CONSTRAINT "SequelizeMeta_pkey" PRIMARY KEY ("name")
);

-- CreateTable
CREATE TABLE "spots" (
    "uuid" UUID NOT NULL,
    "title" VARCHAR(255),
    "description" TEXT,
    "coordinates" geometry(Point, 4326),
    "timestamp" TIMESTAMPTZ(6),
    "redirection" VARCHAR(255),
    "referenced" BOOLEAN,
    "user_username" VARCHAR(255),
    "created_at" TIMESTAMPTZ(6) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMPTZ(6) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "deleted_at" TIMESTAMPTZ(6),

    CONSTRAINT "spots_pkey" PRIMARY KEY ("uuid")
);

-- CreateTable
CREATE TABLE "tokens" (
    "uuid" UUID NOT NULL,
    "hash" VARCHAR(255) NOT NULL,
    "name" VARCHAR(255),
    "user_username" VARCHAR(255),
    "created_at" TIMESTAMPTZ(6) NOT NULL,
    "updated_at" TIMESTAMPTZ(6) NOT NULL,
    "deleted_at" TIMESTAMPTZ(6),

    CONSTRAINT "tokens_pkey" PRIMARY KEY ("uuid","hash")
);

-- CreateTable
CREATE TABLE "users" (
    "username" VARCHAR(255) NOT NULL,
    "email" VARCHAR(255),
    "password" VARCHAR(255),
    "confirmed" BOOLEAN DEFAULT false,
    "created_at" TIMESTAMPTZ(6) NOT NULL,
    "updated_at" TIMESTAMPTZ(6) NOT NULL,
    "deleted_at" TIMESTAMPTZ(6),
    "validate_email" VARCHAR(255),
    "reset_password" VARCHAR(255),

    CONSTRAINT "users_pkey" PRIMARY KEY ("username")
);

-- CreateIndex
CREATE INDEX "spots_idx" ON "spots" USING GIST ("coordinates");

-- CreateIndex
CREATE INDEX "users_reset_password" ON "users"("reset_password");

-- CreateIndex
CREATE INDEX "users_validate_email" ON "users"("validate_email");

-- AddForeignKey
ALTER TABLE "spots" ADD CONSTRAINT "spots_user_username_fkey" FOREIGN KEY ("user_username") REFERENCES "users"("username") ON DELETE NO ACTION ON UPDATE NO ACTION;

-- AddForeignKey
ALTER TABLE "tokens" ADD CONSTRAINT "tokens_user_username_fkey" FOREIGN KEY ("user_username") REFERENCES "users"("username") ON DELETE NO ACTION ON UPDATE NO ACTION;
