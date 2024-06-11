import { Module } from '@nestjs/common';
import { SpotsModule } from '@/spots/spots.module';
import { AuthModule } from '@/auth/auth.module';
import { UsersModule } from '@/users/users.module';
import { AppController } from '@/app-views.controller';
import { ConfigModule } from './config/config.module';
import { TokensModule } from './tokens/tokens.module';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';
import { MailModule } from '@/mail/mail.module';

@Module({
	imports: [
		AuthModule,
		ConfigModule,
		MailModule,
		ResetPasswordRequestsModule,
		SpotsModule,
		UsersModule,
		TokensModule,
	],
	controllers: [AppController],
})
export class AppModule {}
