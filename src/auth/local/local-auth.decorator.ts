/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiForbiddenResponse, ApiUnauthorizedResponse } from '@nestjs/swagger';
import { LocalAuthGuard } from '@/auth/local/local-auth.guard';

export const LocalAuth = (
  ...guards: (Function | CanActivate)[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    UseGuards(LocalAuthGuard, ...guards),
    ApiUnauthorizedResponse({
      description: 'Wrong username/password.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
