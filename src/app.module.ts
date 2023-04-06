import { Module } from '@nestjs/common';
import { SpotsModule } from './spots/spots.module';
import { PrismaModule } from './prisma/prisma.module';
import { AuthModule } from './auth/auth.module';
import { UsersModule } from './users/users.module';
import { AppController } from './app.controller';
import { ConfigModule } from './config/config.module';

@Module({
  imports: [SpotsModule, PrismaModule, AuthModule, UsersModule, ConfigModule],
  controllers: [AppController],
})
export class AppModule {}
