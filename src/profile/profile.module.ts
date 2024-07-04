import { Module } from '@nestjs/common';
import { UsersModule } from '@/users/users.module';
import { ProfileController } from '@/profile/profile.controller';
import { AuthModule } from '@/auth/auth.module';
import { ProfileViewsController } from '@/profile/profile-views.controller';

@Module({
	imports: [AuthModule, UsersModule],
	controllers: [ProfileController, ProfileViewsController],
})
export class ProfileModule {}
