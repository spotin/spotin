import { Module } from '@nestjs/common';
import { PassportModule } from '@nestjs/passport';
import { JwtModule } from '@nestjs/jwt';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AuthApiController } from '@/auth/auth-api.controller';
import { AuthViewsController } from '@/auth/auth-views.controller';
import { LocalStrategy } from '@/auth/local/local.strategy';
import { JwtStrategy } from '@/auth/jwt/jwt.strategy';
import { AuthService } from '@/auth/auth.service';
import { UsersModule } from '@/users/users.module';

@Module({
  imports: [
    ConfigModule,
    JwtModule.registerAsync({
      imports: [ConfigModule],
      inject: [ConfigService],
      useFactory: async (config: ConfigService) => ({
        secret: config.get<string>('SPOT_IN_JWT_SECRET'),
        signOptions: {
          expiresIn: config.get<string>('SPOT_IN_JWT_EXPIRATION_TIME'),
        },
      }),
    }),
    PassportModule,
    UsersModule,
  ],
  providers: [AuthService, JwtStrategy, LocalStrategy],
  controllers: [AuthApiController, AuthViewsController],
  exports: [AuthService],
})
export class AuthModule {}
