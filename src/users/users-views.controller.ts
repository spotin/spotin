import { Get, Controller, Render, Post, UseGuards } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';
import { LocalAuthGuard } from 'src/auth/local/local-auth.guard';

@ApiTags('users')
@Controller('users')
export class UsersViewsController {
  @Get('login')
  @Render('users/login')
  root() {
    return {};
  }
}
