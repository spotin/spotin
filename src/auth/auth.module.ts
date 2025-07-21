import { Module } from '@nestjs/common';
import { PassportModule } from '@nestjs/passport';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AuthController } from '@/auth/auth.controller';
import { AuthViewsController } from '@/auth/auth-views.controller';
import { LocalStrategy } from '@/auth/local/local.strategy';
import { JwtAccessTokenStrategy } from '@/auth/jwt/jwt-access-token.strategy';
import { AuthService } from '@/auth/auth.service';
import { UsersModule } from '@/users/users.module';
import {
	EnvironmentVariables,
	JWT_AUDIENCE,
	JWT_ISSUER,
} from '@/config/config.constants';
import { TokenStrategy } from '@/auth/token/token.strategy';
import { UnconfiguredSpotStrategy } from '@/auth/unconfigured-spot/unconfigured-spot.strategy';
import { SpotsModule } from '@/spots/spots.module';
import { UnrestrictedStrategy } from '@/auth/unrestricted/unrestricted.strategy';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';
import { ResetPasswordStrategy } from '@/auth/reset-password/reset-password.strategy';
import { JwtRefreshTokenStrategy } from '@/auth/jwt/jwt-refresh-token.strategy';
import { JwtModule } from '@nestjs/jwt';

@Module({
	imports: [
		ConfigModule,
		JwtModule.registerAsync({
			imports: [ConfigModule],
			inject: [ConfigService],
			useFactory: (
				configService: ConfigService<EnvironmentVariables, true>,
			) => ({
				signOptions: {
					issuer: configService.get(JWT_ISSUER, { infer: true }),
					audience: configService.get(JWT_AUDIENCE, { infer: true }),
				},
				verifyOptions: {
					issuer: configService.get(JWT_ISSUER, { infer: true }),
					audience: configService.get(JWT_AUDIENCE, { infer: true }),
					ignoreExpiration: false,
				},
			}),
		}),
		PassportModule,
		ResetPasswordRequestsModule,
		UsersModule,
		SpotsModule,
	],
	providers: [
		AuthService,
		JwtAccessTokenStrategy,
		JwtRefreshTokenStrategy,
		LocalStrategy,
		ResetPasswordStrategy,
		TokenStrategy,
		UnconfiguredSpotStrategy,
		UnrestrictedStrategy,
	],
	controllers: [AuthController, AuthViewsController],
	exports: [AuthService],
})
export class AuthModule {}
