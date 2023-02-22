import { Get, Controller, Request, Render, UseGuards } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';

@ApiTags('views')
@Controller()
export class AppController {
  @Get()
  @Render('index')
  root() {
    return {};
  }
}
