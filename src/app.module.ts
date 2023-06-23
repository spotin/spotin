import { Module } from '@nestjs/common';
import { PrismaModule } from 'nestjs-prisma';
import { SpotsModule } from '@/spots/spots.module';
import { AuthModule } from '@/auth/auth.module';
import { UsersModule } from '@/users/users.module';
import { AppMvcController } from '@/app-mvc.controller';
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
  controllers: [AppMvcController],
})
export class AppModule {}
