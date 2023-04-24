import { Module } from '@nestjs/common';
import { PrismaClientExceptionFilter, PrismaModule } from 'nestjs-prisma';
import { APP_FILTER, HttpAdapterHost } from '@nestjs/core';
import { SpotsModule } from '@/spots/spots.module';
import { AuthModule } from '@/auth/auth.module';
import { UsersModule } from '@/users/users.module';
import { AppController } from '@/app.controller';
import { ConfigModule } from './config/config.module';
import { TokensModule } from './tokens/tokens.module';

@Module({
  imports: [
    AuthModule,
    ConfigModule,
    PrismaModule.forRoot(),
    SpotsModule,
    UsersModule,
    TokensModule,
  ],
  controllers: [AppController],
  providers: [
    // Replace with `providePrismaClientExceptionFilter()` once publicly available
    // https://nestjs-prisma.dev/docs/exception-filter/#app_filter
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
