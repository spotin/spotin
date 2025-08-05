import { Module } from '@nestjs/common';
import { SpotsModule } from '@/spots/spots.module';
import { AuthModule } from '@/auth/auth.module';
import { UsersModule } from '@/users/users.module';
import { AppViewsController } from '@/app-views.controller';
import { ConfigModule } from '@/config/config.module';
import { TokensModule } from '@/tokens/tokens.module';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';
import { MailModule } from '@/mail/mail.module';
import { ProfileModule } from '@/profile/profile.module';
import { I18nModule } from '@/i18n/i18n.module';
import { StatsModule } from '@/stats/stats.module';

@Module({
	imports: [
		AuthModule,
		ConfigModule,
		MailModule,
		I18nModule,
		ProfileModule,
		ResetPasswordRequestsModule,
		SpotsModule,
		StatsModule,
		UsersModule,
		TokensModule,
	],
	controllers: [AppViewsController],
})
export class AppModule {}
