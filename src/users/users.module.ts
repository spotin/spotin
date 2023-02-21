import { Module } from '@nestjs/common';
import { UsersService } from './users.service';
import { UsersApiController } from './users-api.controller';
import { UsersViewsController } from './users-views.controller';

@Module({
  controllers: [UsersApiController, UsersViewsController],
  providers: [UsersService],
  exports: [UsersService],
})
export class UsersModule {}
