import { Module } from '@nestjs/common';
import { PrismaModule } from 'nestjs-prisma';
import { UsersService } from '@/users/users.service';
import { UsersApiController } from '@/users/users-api.controller';
import { UsersViewsController } from '@/users/users-views.controller';

@Module({
  imports: [PrismaModule],
  controllers: [UsersApiController, UsersViewsController],
  providers: [UsersService],
  exports: [UsersService],
})
export class UsersModule {}
