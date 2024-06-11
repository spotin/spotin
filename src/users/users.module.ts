import { Module } from '@nestjs/common';
import { PrismaModule } from 'nestjs-prisma';
import { UsersService } from '@/users/users.service';
import { UsersApiController } from '@/users/users-api.controller';
import { UsersMvcController } from '@/users/users-views.controller';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';

@Module({
	imports: [PrismaModule, ResetPasswordRequestsModule],
	controllers: [UsersApiController, UsersMvcController],
	providers: [UsersService],
	exports: [UsersService],
})
export class UsersModule {}
