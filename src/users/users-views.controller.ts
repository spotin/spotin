import { Get, Controller, Render, Post, Body } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';
import { CreateUserDto } from './dto/create-user.dto';
import { UsersService } from './users.service';

@ApiTags('Views')
@Controller()
export class UsersViewsController {
  constructor(private readonly usersService: UsersService) {}

  @Get('users/login')
  @Render('users/login')
  root() {
    return {};
  }

  @Get('users/signup')
  @Render('users/signup')
  signup() {
    return {};
  }

  @Post('users/signup')
  @Render('users/login')
  async register(@Body() createUserDto: CreateUserDto) {
    await this.usersService.create(createUserDto);
  }
}
