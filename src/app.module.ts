import { Module } from '@nestjs/common';
import { SpotsModule } from './spots/spots.module';
import { PrismaModule } from './prisma/prisma.module';
import { AuthModule } from './auth/auth.module';
import { UsersModule } from './users/users.module';
import { AppController } from './app.controller';

@Module({
  imports: [SpotsModule, PrismaModule, AuthModule, UsersModule],
  controllers: [AppController],
})
export class AppModule {}
