import { Module } from '@nestjs/common';
import { PassportModule } from '@nestjs/passport';
import { JwtModule } from '@nestjs/jwt';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AuthController } from '@/auth/auth.controller';
import { AuthViewsController } from '@/auth/auth-views.controller';
import { LocalStrategy } from '@/auth/local/local.strategy';
import { JwtStrategy } from '@/auth/jwt/jwt.strategy';
import { AuthService } from '@/auth/auth.service';
import { UsersModule } from '@/users/users.module';
import {
	EnvironmentVariables,
	JWT_EXPIRATION_TIME,
	JWT_SECRET,
} from '@/config/config.constants';
import { TokenStrategy } from '@/auth/token/token.strategy';
import { UnconfiguredSpotStrategy } from '@/auth/unconfigured-spot/unconfigured-spot.strategy';
import { SpotsModule } from '@/spots/spots.module';
import { UnrestrictedStrategy } from '@/auth/unrestricted/unrestricted.strategy';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';
import { ResetPasswordStrategy } from '@/auth/reset-password/reset-password.strategy';

@Module({
	imports: [
		ConfigModule,
		JwtModule.registerAsync({
			imports: [ConfigModule],
			inject: [ConfigService],
			useFactory: (
				configService: ConfigService<EnvironmentVariables, true>,
			) => ({
				secret: configService.get(JWT_SECRET, { infer: true }),
				signOptions: {
					expiresIn: configService.get(JWT_EXPIRATION_TIME, { infer: true }),
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
		JwtStrategy,
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
