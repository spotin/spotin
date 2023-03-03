import { Get, Controller, Request, Render, UseGuards } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';

@ApiTags('Views')
@Controller()
export class AppController {
  @Get()
  @Render('index')
  root() {
    return;
  }
}
