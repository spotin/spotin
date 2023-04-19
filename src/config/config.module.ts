import * as Joi from 'joi';
import { Module } from '@nestjs/common';
import { ConfigModule as NestConfigModule } from '@nestjs/config';
import { ConfigConfiguration } from '@/config/config.configuration';
import {
  FQDN,
  JWT_SECRET,
  JWT_EXPIRATION_TIME,
  NODE_ENV,
} from '@/config/config.constants';

@Module({
  imports: [
    NestConfigModule.forRoot({
      load: [ConfigConfiguration],
      validationSchema: Joi.object({
        [FQDN]: Joi.string().required(),
        [JWT_SECRET]: Joi.string().required(),
        [JWT_EXPIRATION_TIME]: Joi.string().required(),
        [NODE_ENV]: Joi.string()
          .valid('development', 'test', 'production')
          .default('production'),
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
