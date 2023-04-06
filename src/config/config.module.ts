import { Module } from '@nestjs/common';
import { ConfigModule as NestConfigModule } from '@nestjs/config';
import * as Joi from 'joi';

@Module({
  imports: [
    NestConfigModule.forRoot({
      validationSchema: Joi.object({
        SPOT_IN_BACKEND_URL: Joi.string().required(),
        SPOT_IN_JWT_SECRET: Joi.string().required(),
        SPOT_IN_JWT_EXPIRATION_TIME: Joi.string().required(),
      }),

      validationOptions: {
        allowUnknown: true,
        abortEarly: false,
      },
    }),
  ],
  exports: [NestConfigModule],
})
export class ConfigModule {}
