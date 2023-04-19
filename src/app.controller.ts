import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { Roles } from '@/auth/decorators/roles.decorator';
import { RolesGuard } from '@/auth/guards/roles.guard';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Get, Controller, Render } from '@nestjs/common';
import { ApiOkResponse, ApiOperation, ApiTags } from '@nestjs/swagger';
import { User, UserRole } from '@prisma/client';

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
    console.log(user);
    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
    };
  }
}
