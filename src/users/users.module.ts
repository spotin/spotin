import { Module } from '@nestjs/common';
import { PrismaModule } from 'nestjs-prisma';
import { UsersService } from '@/users/users.service';
import { UsersController } from '@/users/users.controller';
import { UsersViewsController } from '@/users/users-views.controller';
import { ResetPasswordRequestsModule } from '@/reset-password-requests/reset-password-requests.module';

@Module({
	imports: [PrismaModule, ResetPasswordRequestsModule],
	controllers: [UsersController, UsersViewsController],
	providers: [UsersService],
	exports: [UsersService],
})
export class UsersModule {}
