import { Module } from '@nestjs/common';
import { PassportModule } from '@nestjs/passport';
import { ConfigModule } from '@nestjs/config';
import { AuthController } from '@/auth/auth.controller';
import { AuthViewsController } from '@/auth/auth-views.controller';
import { EmailPasswordStrategy } from '@/auth/email-password/email-password.strategy';
import { AuthService } from '@/auth/auth.service';
import { UsersModule } from '@/users/users.module';
import { TokenStrategy } from '@/auth/token/token.strategy';
import { UnconfiguredSpotStrategy } from '@/auth/unconfigured-spot/unconfigured-spot.strategy';
import { SpotsModule } from '@/spots/spots.module';
import { UnrestrictedStrategy } from '@/auth/unrestricted/unrestricted.strategy';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';
import { ResetPasswordStrategy } from '@/auth/reset-password/reset-password.strategy';
import { SessionSerializer } from '@/auth/session/session.serializer';

@Module({
	imports: [
		ConfigModule,
		PassportModule,
		ResetPasswordRequestsModule,
		UsersModule,
		SpotsModule,
	],
	providers: [
		AuthService,
		EmailPasswordStrategy,
		ResetPasswordStrategy,
		SessionSerializer,
		TokenStrategy,
		UnconfiguredSpotStrategy,
		UnrestrictedStrategy,
	],
	controllers: [AuthController, AuthViewsController],
	exports: [AuthService],
})
export class AuthModule {}
