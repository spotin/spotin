import { Roles } from '@/auth/decorators/roles.decorator';
import { RolesGuard } from '@/auth/roles/roles.guard';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { Controller } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';
import { UserRole } from '@prisma/client';

@ApiTags('Views')
@Controller('users')
@JwtAuth(RolesGuard)
@Roles(UserRole.ADMIN)
export class UsersViewsController {}
