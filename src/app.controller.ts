import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { Get, Controller, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';
import { User } from '@prisma/client';

@ApiTags('Views')
@Controller()
export class AppController {
  @Get()
  @ApiOperation({
    summary: 'Render the main page',
    description: 'Render the main page.',
    operationId: 'root',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('index')
  root(@AuthUser() user: User) {
    return {
      title: 'Home - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
    };
  }

  @Get('api')
  @ApiOperation({
    summary: 'Render the Swagger UI page',
    description: 'Render the Swagger UI page.',
    operationId: 'api',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  api() {
    return;
  }

  @Get('not-found')
  @ApiOperation({
    summary: 'Render the not found page',
    description: 'Render the not found page.',
    operationId: 'notFound',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('not-found')
  notFound(@AuthUser() user: User) {
    return {
      title: 'Not Found - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
    };
  }
}
