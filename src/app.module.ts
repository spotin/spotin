import { Module } from '@nestjs/common';
import { SpotsModule } from './spots/spots.module';
import { AuthModule } from './auth/auth.module';
import { UsersModule } from './users/users.module';
import { AppController } from './app.controller';
import { ConfigModule } from './config/config.module';
import { PrismaClientExceptionFilter, PrismaModule } from 'nestjs-prisma';
import { APP_FILTER, HttpAdapterHost } from '@nestjs/core';

@Module({
  imports: [AuthModule, ConfigModule, PrismaModule, SpotsModule, UsersModule],
  controllers: [AppController],
  providers: [
    // Replace with `providePrismaClientExceptionFilter()` once publicetly available
    // https://nestjs-prisma.dev/docs/exception-filter/
    {
      provide: APP_FILTER,
      useFactory: ({ httpAdapter }: HttpAdapterHost) => {
        return new PrismaClientExceptionFilter(httpAdapter);
      },
      inject: [HttpAdapterHost],
    },
  ],
})
export class AppModule {}
