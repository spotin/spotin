import { Module } from '@nestjs/common';
import { UsersModule } from '@/users/users.module';
import { ProfileController } from '@/profile/profile.controller';
import { AuthModule } from '@/auth/auth.module';
import { ProfileViewsController } from '@/profile/profile-views.controller';
import { MarkdownModule } from '@/markdown/markdown.module';

@Module({
	imports: [AuthModule, MarkdownModule, UsersModule],
	controllers: [ProfileController, ProfileViewsController],
})
export class ProfileModule {}
