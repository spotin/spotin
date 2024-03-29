import { Module } from '@nestjs/common';
import { PassportModule } from '@nestjs/passport';
import { JwtModule } from '@nestjs/jwt';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AuthApiController } from '@/auth/auth-api.controller';
import { AuthMvcController } from '@/auth/auth-mvc.controller';
import { LocalStrategy } from '@/auth/local/local.strategy';
import { JwtStrategy } from '@/auth/jwt/jwt.strategy';
import { AuthService } from '@/auth/auth.service';
import { UsersModule } from '@/users/users.module';
import { JWT_EXPIRATION_TIME, JWT_SECRET } from '@/config/config.constants';
import { TokenStrategy } from '@/auth/token/token.strategy';
import { UnconfiguredSpotStrategy } from '@/auth/unconfigured-spot/unconfigured-spot.strategy';
import { SpotsModule } from '@/spots/spots.module';
import { UnrestrictedStrategy } from '@/auth/unrestricted/unrestricted.strategy';

@Module({
  imports: [
    ConfigModule,
    JwtModule.registerAsync({
      imports: [ConfigModule],
      inject: [ConfigService],
      useFactory: async (configService: ConfigService) => ({
        secret: configService.get(JWT_SECRET, { infer: true }),
        signOptions: {
          expiresIn: configService.get(JWT_EXPIRATION_TIME, { infer: true }),
        },
      }),
    }),
    PassportModule,
    UsersModule,
    SpotsModule,
  ],
  providers: [
    AuthService,
    JwtStrategy,
    LocalStrategy,
    TokenStrategy,
    UnconfiguredSpotStrategy,
    UnrestrictedStrategy,
  ],
  controllers: [AuthApiController, AuthMvcController],
  exports: [AuthService],
})
export class AuthModule {}
