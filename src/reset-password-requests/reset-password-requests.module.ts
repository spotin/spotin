import { Module } from '@nestjs/common';
import { PrismaModule } from 'nestjs-prisma';
import { ResetPasswordRequestsService } from '@/reset-password-requests/reset-password-requests.service';
import { MailModule } from '@/mail/mail.module';

@Module({
	imports: [MailModule, PrismaModule],
	providers: [ResetPasswordRequestsService],
	exports: [ResetPasswordRequestsService],
})
export class ResetPasswordRequestsModule {}
